from fastapi import APIRouter, Depends
from sqlalchemy import select, func
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.orm import Session
from typing import List, Union
from api.database.session import get_db
from api.models import models
from api.schemas.guest import GuestStatsResponse, GuestInputDate, GuestRequest
from pydantic import BaseModel, validator
from datetime import datetime
import csv
import io
from fastapi.responses import StreamingResponse
from api.dependencies.middleware import verify_api_key
from api.utils.timezone import now

router = APIRouter()

@router.post("/", response_model=List[GuestStatsResponse])
async def get_guests_data(
    reportdate: GuestInputDate,
    db: Union[Session, AsyncSession] = Depends(get_db),
    user: models.User = Depends(verify_api_key)
):
    stmt = (
        select(
            models.Guest.name,
            models.Guest.email,
            models.Guest.username,
            models.Guest.mac_add,
            models.Guest.os_client,
            models.Guest.browser_client,
            models.Guest.device_client,
            models.Guest.brand_client,
            models.Guest.model_client,
            models.Guest.device_type,
            models.Guest.created_at,
            models.Guest.updated_at,
            func.sum(models.Radacct.acctinputoctets).label("byteinput"),
            func.sum(models.Radacct.acctoutputoctets).label("byteoutput"),
            models.Country.country_name
        )
        .join(models.Radacct, models.Guest.username == models.Radacct.username)
        .join(models.Country, models.Guest.country_id == models.Country.id)
        .filter(models.Radacct.acctstarttime >= reportdate.startdate)
        .filter(models.Radacct.acctstarttime < reportdate.enddate)
        .group_by(models.Radacct.username)
        .order_by(models.Guest.created_at.asc())
    )

    # Cek apakah session yang dipakai async atau sync
    if isinstance(db, AsyncSession):
        result = await db.execute(stmt)
    else:
        result = db.execute(stmt)

    return result.all()

# @router.post("/export-csv")
# async def export_guests_csv(
#     reportdate: GuestInputDate,
#     db: Union[Session, AsyncSession] = Depends(get_db),
#     user: models.User = Depends(verify_api_key)
# ):
#     stmt = (
#         select(
#             models.Guest.name,
#             models.Guest.email,
#             models.Guest.username,
#             models.Guest.mac_add,
#             models.Guest.os_client,
#             models.Guest.browser_client,
#             models.Guest.device_client,
#             models.Guest.brand_client,
#             models.Guest.model_client,
#             models.Guest.device_type,
#             models.Guest.created_at,
#             models.Guest.updated_at,
#             func.sum(models.Radacct.acctinputoctets).label("byteinput"),
#             func.sum(models.Radacct.acctoutputoctets).label("byteoutput"),
#             models.Country.country_name
#         )
#         .join(models.Radacct, models.Guest.username == models.Radacct.username)
#         .join(models.Country, models.Guest.country_id == models.Country.id)
#         .filter(models.Radacct.acctstarttime >= reportdate.startdate)
#         .filter(models.Radacct.acctstarttime <= reportdate.enddate)
#         .group_by(models.Radacct.username)
#         .order_by(models.Guest.created_at.asc())
#     )

#     if isinstance(db, AsyncSession):
#         result = await db.execute(stmt)
#     else:
#         result = db.execute(stmt)

#     rows = result.all()

#     if not rows:
#         raise HTTPException(status_code=404, detail="Data was not found within the given date range.")

#     # Buat CSV
#     output = io.StringIO()
#     writer = csv.writer(output)

#     # Header CSV
#     writer.writerow([
#         "Name", "Email", "Username", "MAC Address", "OS", "Browser",
#         "Device", "Brand", "Model", "Device Type", "First Connect", "Last Connect",
#         "Byte Input", "Byte Output", "Country"
#     ])

#     # Isi CSV
#     for row in rows:
#         writer.writerow(row)

#     output.seek(0)

#     filename = f"guests_{reportdate.startdate}_to_{reportdate.enddate}.csv"
#     return StreamingResponse(
#         output,
#         media_type="text/csv",
#         headers={"Content-Disposition": f"attachment; filename={filename}"}
#     )


from datetime import datetime, timedelta

@router.post("/export-csv")
async def export_guests_csv(
    reportdate: GuestInputDate,
    db: Union[Session, AsyncSession] = Depends(get_db),
    user: models.User = Depends(verify_api_key)
):
    # Pastikan enddate mencakup akhir hari
    start_dt = datetime.combine(reportdate.startdate, datetime.min.time())
    end_dt = datetime.combine(reportdate.enddate, datetime.max.time())

    stmt = (
        select(
            models.Guest.name,
            models.Guest.email,
            models.Guest.username,
            models.Guest.mac_add,
            models.Guest.os_client,
            models.Guest.browser_client,
            models.Guest.device_client,
            models.Guest.brand_client,
            models.Guest.model_client,
            models.Guest.device_type,
            models.Guest.created_at,
            models.Guest.updated_at,
            func.sum(models.Radacct.acctinputoctets).label("byteinput"),
            func.sum(models.Radacct.acctoutputoctets).label("byteoutput"),
            models.Country.country_name
        )
        .join(models.Radacct, models.Guest.username == models.Radacct.username)
        .join(models.Country, models.Guest.country_id == models.Country.id)
        .filter(models.Radacct.acctstarttime >= start_dt)
        .filter(models.Radacct.acctstarttime <= end_dt)
        .group_by(models.Radacct.username)
        .order_by(models.Guest.created_at.asc())
    )

    if isinstance(db, AsyncSession):
        result = await db.execute(stmt)
    else:
        result = db.execute(stmt)

    rows = result.all()

    if not rows:
        raise HTTPException(status_code=404, detail="Data was not found within the given date range.")

    # Buat CSV
    output = io.StringIO()
    writer = csv.writer(output)

    # Header CSV
    writer.writerow([
        "Name", "Email", "Username", "MAC Address", "OS", "Browser",
        "Device", "Brand", "Model", "Device Type", "First Connect", "Last Connect",
        "Byte Input", "Byte Output", "Country"
    ])

    # Isi CSV
    for row in rows:
        writer.writerow(row)

    output.seek(0)

    filename = f"guests_{reportdate.startdate}_to_{reportdate.enddate}.csv"
    return StreamingResponse(
        output,
        media_type="text/csv",
        headers={"Content-Disposition": f"attachment; filename={filename}"}
    )



from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select
from api.database.session import get_db
from api.models.models import Guest, Radcheck
from api.schemas.guest import GuestCreate
from api.utils.security import (
    generate_random_username,
    generate_random_password,
)
import uuid


@router.post("/create")
async def create_guest(payload: GuestCreate, db: AsyncSession = Depends(get_db)):
    # Cek email sudah ada?
    result = await db.execute(
        select(Guest).where(Guest.email == payload.email)
    )
    existing_email = result.scalar_one_or_none()
    if existing_email:
        radcheck = await db.execute(
            select(Radcheck).where(Radcheck.username == existing_email.username )
        )
        existing_radcheck = radcheck.scalar_one_or_none()
        if (not existing_radcheck):
            new_radcheck = Radcheck(
                username=existing_email.username,
                attribute="Cleartext-Password",
                op=":=",
                value=existing_email.password
            )
            db.add(new_radcheck)
            await db.commit()
            await db.refresh(new_radcheck)
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Email sudah terdaftar."
        )

    # Generate username unik & password random
    username = await generate_random_username(db,10)
    password_plain = generate_random_password(10)

    new_guest = Guest(
        id=uuid.uuid4(),
        name=payload.name,
        email=payload.email,
        country_id=payload.country_id,
        username=username,
        password=password_plain,
        mac_add=payload.mac_add,
        os_client=payload.os_client,
        browser_client=payload.browser_client,
        device_client=payload.device_client,
        brand_client=payload.brand_client,
        model_client=payload.model_client,
        device_type=payload.device_type,
        created_at=now(),
        updated_at=now()
    )

    db.add(new_guest)
    await db.commit()
    await db.refresh(new_guest)

    new_radcheck = Radcheck(
                username=username,
                attribute="Cleartext-Password",
                op=":=",
                value=password_plain
            )
    db.add(new_radcheck)
    await db.commit()
    await db.refresh(new_radcheck)
    return {
        "message": "Guest berhasil dibuat",
        "username": username,
        "password": password_plain  # hanya ditampilkan sekali
    }


from api.utils.security import role_required

@router.get("/")
async def get_guest(params: GuestRequest = Depends(), db: AsyncSession = Depends(get_db), user=Depends(role_required(["admin", "operator","user"]))):
    stmt = select(Guest)
    if params.email:
        stmt = stmt.where(Guest.email == params.email)
    elif params.mac_add:
        stmt = stmt.where(Guest.mac_add == params.mac_add)
    else:
        raise HTTPException(
            status_code=400,
            detail="Harus menyertakan email atau mac_add"
        )
    result = await db.execute(stmt)
    guest = result.scalars().first()
    if not guest:
        # raise HTTPException(status_code=404, detail="Guest tidak ditemukan")
        # data['exist'] = False
        # data['msg'] = None

        return {
            'error': False,
            'exist': False,
            'msg': None
        }
    
    return {
        'error': False,
        'exist': True,
        'msg': {
            'name': guest.name,
            'username': guest.username,
            'password': guest.password
        }
    }


""" 
#########################################################
Mendapatkan data guest dengan parameter mac address
#########################################################
"""

@router.get("/mac/{mac_add}", response_model=GuestStatsResponse)
async def get_guest_by_mac(
    mac_add: str,
    db: AsyncSession = Depends(get_db),
    # user=Depends(role_required(["admin", "operator", "user"]))
):
    stmt = select(Guest).where(Guest.mac_add == mac_add)
    result = await db.execute(stmt)
    guest = result.scalars().first()
    
    if not guest:
        raise HTTPException(status_code=404, detail="Guest tidak ditemukan")
    
    return guest

""" 
#########################################################
Mendapatkan data guest dengan parameter email
#########################################################
"""

@router.get("/email")
async def get_guest_by_email(
    email: str,  # otomatis jadi query parameter
    db: AsyncSession = Depends(get_db),
):
    stmt = select(Guest).where(Guest.email == email)
    result = await db.execute(stmt)
    guest = result.scalars().first()
    
    if not guest:
        raise HTTPException(status_code=404, detail="Guest tidak ditemukan")
    
    return guest

# @router.get("/all", response_model=List[GuestStatsResponse])
# async def get_guests_data(
#     db: Union[Session, AsyncSession] = Depends(get_db),
#     user=Depends(role_required(["admin", "operator","user"]))
# ):
#     stmt = (
#         select(
#             models.Guest.name,
#             models.Guest.email,
#             models.Guest.username,
#             models.Guest.mac_add,
#             models.Guest.os_client,
#             models.Guest.browser_client,
#             models.Guest.device_client,
#             models.Guest.brand_client,
#             models.Guest.model_client,
#             models.Guest.device_type,
#             models.Guest.created_at,
#             models.Guest.updated_at,
#             func.sum(models.Radacct.acctinputoctets).label("byteinput"),
#             func.sum(models.Radacct.acctoutputoctets).label("byteoutput"),
#             models.Country.country_name
#         )
#         .join(models.Radacct, models.Guest.username == models.Radacct.username)
#         .join(models.Country, models.Guest.country_id == models.Country.id)
#         .group_by(models.Radacct.username)
#         .order_by(models.Guest.created_at.asc())
#     )

#     # Cek apakah session yang dipakai async atau sync
#     if isinstance(db, AsyncSession):
#         result = await db.execute(stmt)
#     else:
#         result = db.execute(stmt)

#     return result.all()

# from fastapi import Query
# from typing import Union
# from sqlalchemy import select, func
# from sqlalchemy.ext.asyncio import AsyncSession
# from sqlalchemy.orm import Session

# from api.models import models
# from ..schemas.guest import GuestStatsResponse, PaginatedGuestResponse

# @router.get("/all", response_model=PaginatedGuestResponse)
# async def get_guests_data(
#     page: int = Query(1, ge=1, description="Data page, starting from 1"),
#     page_size: int = Query(10, ge=1, le=100, description="Number of data per page"),
#     db: Union[Session, AsyncSession] = Depends(get_db),
#     user=Depends(role_required(["admin", "operator", "user"]))
# ):
#     offset = (page - 1) * page_size

#     stmt = (
#         select(
#             models.Guest.name,
#             models.Guest.email,
#             models.Guest.username,
#             models.Guest.mac_add,
#             models.Guest.os_client,
#             models.Guest.browser_client,
#             models.Guest.device_client,
#             models.Guest.brand_client,
#             models.Guest.model_client,
#             models.Guest.device_type,
#             models.Guest.created_at,
#             models.Guest.updated_at,
#             func.sum(models.Radacct.acctinputoctets).label("byteinput"),
#             func.sum(models.Radacct.acctoutputoctets).label("byteoutput"),
#             models.Country.country_name
#         )
#         .join(models.Radacct, models.Guest.username == models.Radacct.username)
#         .join(models.Country, models.Guest.country_id == models.Country.id)
#         .group_by(models.Radacct.username)
#     )

#     # Count total
#     count_stmt = select(func.count()).select_from(stmt.subquery())

#     if isinstance(db, AsyncSession):
#         total_result = await db.execute(count_stmt)
#         total = total_result.scalar_one()
#         result = await db.execute(
#             stmt.order_by(models.Guest.created_at.asc()).limit(page_size).offset(offset)
#         )
#     else:
#         total_result = db.execute(count_stmt)
#         total = total_result.scalar_one()
#         result = db.execute(
#             stmt.order_by(models.Guest.created_at.asc()).limit(page_size).offset(offset)
#         )

#     items = result.all()
#     total_pages = (total + page_size - 1) // page_size

#     return PaginatedGuestResponse(
#         total=total,
#         page=page,
#         page_size=page_size,
#         total_pages=total_pages,
#         items=items
#     )


from fastapi import Query
from typing import Union, Optional
from datetime import datetime
from sqlalchemy import select, func, or_, asc, desc
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.orm import Session

from api.models import models
from api.schemas.guest import GuestStatsResponse, PaginatedGuestResponse

@router.get("/all", response_model=PaginatedGuestResponse)
async def get_guests_data(
    page: int = Query(1, ge=1, description="Halaman data, mulai dari 1"),
    page_size: int = Query(10, ge=1, le=100, description="Jumlah data per halaman"),
    search: Optional[str] = Query(None, description="Cari di name, email, mac_add, username"),
    country_id: Optional[int] = Query(None, description="Filter berdasarkan ID negara"),
    date_from: Optional[datetime] = Query(None, description="Filter dari tanggal (created_at)"),
    date_to: Optional[datetime] = Query(None, description="Filter sampai tanggal (created_at)"),
    sort_by: Optional[str] = Query("created_at", description="Kolom untuk sorting, pisahkan dengan koma jika lebih dari satu"),
    order: Optional[str] = Query("asc", description="Arah sorting: asc atau desc"),
    db: Union[Session, AsyncSession] = Depends(get_db),
    user=Depends(role_required(["admin", "operator", "user"]))
):
    offset = (page - 1) * page_size

    stmt = (
        select(
            models.Guest.name,
            models.Guest.email,
            models.Guest.username,
            models.Guest.mac_add,
            models.Guest.os_client,
            models.Guest.browser_client,
            models.Guest.device_client,
            models.Guest.brand_client,
            models.Guest.model_client,
            models.Guest.device_type,
            models.Guest.created_at,
            models.Guest.updated_at,
            func.sum(models.Radacct.acctinputoctets).label("byteinput"),
            func.sum(models.Radacct.acctoutputoctets).label("byteoutput"),
            models.Country.country_name
        )
        .join(models.Radacct, models.Guest.username == models.Radacct.username)
        .join(models.Country, models.Guest.country_id == models.Country.id)
        .group_by(models.Radacct.username)
    )

    # 🔎 Search ke banyak kolom
    if search:
        stmt = stmt.filter(
            or_(
                models.Guest.name.ilike(f"%{search}%"),
                models.Guest.email.ilike(f"%{search}%"),
                models.Guest.mac_add.ilike(f"%{search}%"),
                models.Guest.username.ilike(f"%{search}%"),
            )
        )

    if country_id:
        stmt = stmt.filter(models.Guest.country_id == country_id)

    if date_from:
        stmt = stmt.filter(models.Guest.created_at >= date_from)

    if date_to:
        stmt = stmt.filter(models.Guest.created_at <= date_to)

    # ✅ Mapping field yang bisa di-sort
    valid_sort_fields = {
        "name": models.Guest.name,
        "email": models.Guest.email,
        "username": models.Guest.username,
        "mac_add": models.Guest.mac_add,
        "created_at": models.Guest.created_at,
        "updated_at": models.Guest.updated_at,
        "byteinput": func.sum(models.Radacct.acctinputoctets),
        "byteoutput": func.sum(models.Radacct.acctoutputoctets),
    }

    # Multi-sort handling
    sort_columns: List = []
    for col in sort_by.split(","):
        col = col.strip()
        if col in valid_sort_fields:
            if order.lower() == "desc":
                sort_columns.append(desc(valid_sort_fields[col]))
            else:
                sort_columns.append(asc(valid_sort_fields[col]))

    # Default sorting jika tidak ada yang valid
    if not sort_columns:
        sort_columns = [asc(models.Guest.created_at)]

    stmt = stmt.order_by(*sort_columns)

    # Hitung total
    count_stmt = select(func.count()).select_from(stmt.subquery())

    if isinstance(db, AsyncSession):
        total_result = await db.execute(count_stmt)
        total = total_result.scalar_one()
        result = await db.execute(stmt.limit(page_size).offset(offset))
    else:
        total_result = db.execute(count_stmt)
        total = total_result.scalar_one()
        result = db.execute(stmt.limit(page_size).offset(offset))

    items = result.all()
    total_pages = (total + page_size - 1) // page_size

    return PaginatedGuestResponse(
        total=total,
        page=page,
        page_size=page_size,
        total_pages=total_pages,
        items=items
    )

