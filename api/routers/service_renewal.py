from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.future import select
from typing import List

# Import komponen internal sesuai struktur project Anda
from api.models.models import ServiceRenewalModel
from api.schemas.service_renewal import (
    ServiceRenewalCreate, 
    ServiceRenewalUpdate, 
    ServiceRenewalResponse
)
from api.database.session import get_db
from api.utils.timezone import now
from api.utils.security import role_required

router = APIRouter()

# ==========================================
# 1. CREATE SERVICE RENEWAL
# ==========================================
@router.post("/", response_model=dict, status_code=status.HTTP_201_CREATED)
async def create_service(
    payload: ServiceRenewalCreate, 
    db: AsyncSession = Depends(get_db),
    user = Depends(role_required(["admin", "operator"]))  # Menyesuaikan role yang diizinkan menulis data
):
    # Cek duplikasi service_code (Unique Constraint)
    result = await db.execute(
        select(ServiceRenewalModel).where(ServiceRenewalModel.service_code == payload.service_code)
    )
    if result.scalar_one_or_none():
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST, 
            detail=f"Service code '{payload.service_code}' sudah terdaftar."
        )

    # Inisialisasi model baru dengan waktu dari utilitas project Anda
    current_time = now()
    new_service = ServiceRenewalModel(
        service_code=payload.service_code,
        service_name=payload.service_name,
        service_description=payload.service_description,
        service_price=payload.service_price,
        service_start_date=payload.service_start_date,
        service_end_date=payload.service_end_date,
        service_status=payload.service_status,
        created_at=current_time,
        updated_at=current_time
    )

    db.add(new_service)
    await db.commit()
    await db.refresh(new_service)

    return {
        "message": "Success",
        "service": new_service
    }


# ==========================================
# 2. READ ALL SERVICES
# ==========================================
@router.get("/", response_model=List[ServiceRenewalResponse])
async def get_services(
    db: AsyncSession = Depends(get_db),
    # user = Depends(role_required(["admin", "operator", "user"]))
):
    result = await db.execute(select(ServiceRenewalModel))
    services = result.scalars().all()
    return services


# ==========================================
# 3. READ SERVICE BY ID
# ==========================================
@router.get("/{service_id}", response_model=ServiceRenewalResponse)
async def get_service(
    service_id: int, 
    db: AsyncSession = Depends(get_db),
    user = Depends(role_required(["admin", "operator", "user"]))
):
    result = await db.execute(
        select(ServiceRenewalModel).where(ServiceRenewalModel.id == service_id)
    )
    service = result.scalar_one_or_none()
    if not service:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND, 
            detail=f"Service dengan ID {service_id} tidak ditemukan."
        )
    return service


# ==========================================
# 4. UPDATE SERVICE
# ==========================================
@router.put("/{service_id}", response_model=ServiceRenewalResponse)
async def update_service(
    service_id: int, 
    payload: ServiceRenewalUpdate, 
    db: AsyncSession = Depends(get_db),
    user = Depends(role_required(["admin", "operator"]))
):
    result = await db.execute(
        select(ServiceRenewalModel).where(ServiceRenewalModel.id == service_id)
    )
    service = result.scalar_one_or_none()
    if not service:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND, 
            detail=f"Service dengan ID {service_id} tidak ditemukan."
        )

    # Validasi jika merubah kode servis agar tidak bentrok dengan data lain
    if payload.service_code is not None and payload.service_code != service.service_code:
        code_result = await db.execute(
            select(ServiceRenewalModel).where(
                ServiceRenewalModel.service_code == payload.service_code,
                ServiceRenewalModel.id != service_id
            )
        )
        if code_result.scalar_one_or_none():
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=f"Service code '{payload.service_code}' sudah digunakan oleh data lain."
            )

    # Pemetaan update secara berkala (Pola conditional checking sesuai user.py)
    if payload.service_code is not None:
        service.service_code = payload.service_code
    if payload.service_name is not None:
        service.service_name = payload.service_name
    if payload.service_description is not None:
        service.service_description = payload.service_description
    if payload.service_price is not None:
        service.service_price = payload.service_price
    if payload.service_start_date is not None:
        service.service_start_date = payload.service_start_date
    if payload.service_end_date is not None:
        service.service_end_date = payload.service_end_date
    if payload.service_status is not None:
        service.service_status = payload.service_status
    
    # Isi timestamp updated_at secara manual menggunakan utilitas internal Anda
    service.updated_at = now()

    await db.commit()
    await db.refresh(service)
    return service


# ==========================================
# 5. DELETE SERVICE
# ==========================================
@router.delete("/{service_id}")
async def delete_service(
    service_id: int, 
    db: AsyncSession = Depends(get_db),
    user = Depends(role_required(["admin"])) # Biasanya operasi hapus dibatasi hanya untuk admin
):
    result = await db.execute(
        select(ServiceRenewalModel).where(ServiceRenewalModel.id == service_id)
    )
    service = result.scalar_one_or_none()
    if not service:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND, 
            detail=f"Service dengan ID {service_id} tidak ditemukan."
        )
    
    await db.delete(service)
    await db.commit()
    return {"message": "Service deleted successfully"}