from pydantic import BaseModel, EmailStr, Field
from datetime import datetime, date
from typing import Optional
from pydantic import validator

class GuestStatsResponse(BaseModel):
    name: str
    email: str
    username: str
    mac_add: Optional[str] = None
    os_client: Optional[str] = None
    browser_client: Optional[str] = None
    device_client: Optional[str] = None
    brand_client: Optional[str] = None
    model_client: Optional[str] = None
    device_type: Optional[str] = None
    created_at: Optional[datetime] = None
    updated_at: Optional[datetime] = None
    byteinput: int
    byteoutput: int
    country_name: str

    class Config:
        orm_mode = True

class GuestInputDate(BaseModel):
    startdate: date
    enddate: date

    @validator("enddate")
    def validate_date_range(cls, v, values):
        start = values.get("startdate")
        if start and v < start:
            raise ValueError("startdate must not be later than enddate.")
        return v

class GuestCreate(BaseModel):
    name: str
    email: EmailStr
    country_id: int
    username: Optional[str] = None
    password: Optional[str] = None
    mac_add: Optional[str] = None
    os_client: Optional[str] = None
    browser_client: Optional[str] = None
    device_client: Optional[str] = None
    brand_client: Optional[str] = None
    model_client: Optional[str] = None
    device_type: Optional[str] = None
    created_at: Optional[datetime] = None
    updated_at: Optional[datetime] = None

class GuestRequest(BaseModel):
    email: Optional[str] = None
    mac_add: Optional[str] = None

class GuestResponse(BaseModel):
    name: str
    email: str
    username: str
    password: str
    mac_add: str

