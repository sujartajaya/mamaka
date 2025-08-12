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

@router.post("/export-csv")
async def export_guests_csv(
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
        .filter(models.Radacct.acctstarttime <= reportdate.enddate)
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