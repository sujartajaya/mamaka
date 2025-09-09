from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.future import select
from typing import List

from api.models import models
from api.schemas.client_device import ClientDeviceCreate, ClientDeviceUpdate, ClientDeviceResponse
from api.database.session import get_db
from api.utils.timezone import now  # fungsi helper untuk waktu lokal (kalau ada)
from api.dependencies.middleware import verify_api_key

# router = APIRouter(prefix="/client-devices", tags=["Client Devices"])
router = APIRouter()

# ✅ Create
@router.post("/", response_model=ClientDeviceResponse)
async def create_device(data: ClientDeviceCreate, db: AsyncSession = Depends(get_db), user: models.User = Depends(verify_api_key)):
    new_device = models.ClientDevice(
        **data.dict(),
        created_at=now(),
        updated_at=now()
    )
    db.add(new_device)
    await db.commit()
    await db.refresh(new_device)
    return new_device


# ✅ Read All
@router.get("/", response_model=List[ClientDeviceResponse])
async def get_devices(db: AsyncSession = Depends(get_db), user: models.User = Depends(verify_api_key)):
    result = await db.execute(select(models.ClientDevice))
    return result.scalars().all()


# ✅ Read by ID
@router.get("/{device_id}", response_model=ClientDeviceResponse)
async def get_device(device_id: int, db: AsyncSession = Depends(get_db), user: models.User = Depends(verify_api_key)):
    device = await db.get(models.ClientDevice, device_id)
    if not device:
        raise HTTPException(status_code=404, detail="Device not found")
    return device


# ✅ Update
@router.put("/{device_id}", response_model=ClientDeviceResponse)
async def update_device(device_id: int, data: ClientDeviceUpdate, db: AsyncSession = Depends(get_db), user: models.User = Depends(verify_api_key)):
    device = await db.get(models.ClientDevice, device_id)
    if not device:
        raise HTTPException(status_code=404, detail="Device not found")

    for key, value in data.dict(exclude_unset=True).items():
        setattr(device, key, value)
    device.updated_at = now()

    db.add(device)
    await db.commit()
    await db.refresh(device)
    return device


# ✅ Delete
@router.delete("/{device_id}")
async def delete_device(device_id: int, db: AsyncSession = Depends(get_db), user: models.User = Depends(verify_api_key)):
    device = await db.get(models.ClientDevice, device_id)
    if not device:
        raise HTTPException(status_code=404, detail="Device not found")

    await db.delete(device)
    await db.commit()
    return {"message": "Device deleted successfully"}


from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from api.models.models import Guest, ClientDevice
from api.schemas.client_device import GuestWithDevices

@router.get("/mac-add/{mac_add}", response_model=GuestWithDevices)
async def search_by_mac(mac_add: str, db: AsyncSession = Depends(get_db), user: models.User = Depends(verify_api_key)):
    # Join Guest dengan ClientDevice lewat username
    stmt = (
        select(Guest, ClientDevice)
        .join(ClientDevice, Guest.username == ClientDevice.username)
        .where(ClientDevice.mac_add == mac_add)
    )

    result = await db.execute(stmt)
    row = result.first()

    if not row:
        raise HTTPException(status_code=404, detail="Device/Guest not found")

    guest, device = row

    # Tambahkan devices ke guest supaya sesuai schema
    guest.devices = [device]

    return guest
