from fastapi import APIRouter, Depends
from sqlalchemy import select, func
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.orm import Session
from typing import List, Union
from api.database.session import get_db
from api.models import models
from api.schemas.guest import GuestStatsResponse, GuestInputDate
from pydantic import BaseModel, validator
from datetime import datetime
import csv
import io
from fastapi.responses import StreamingResponse
from api.dependecies.middleware import verify_api_key

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