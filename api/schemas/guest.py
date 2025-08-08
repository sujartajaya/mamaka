from pydantic import BaseModel
from datetime import datetime, date
from typing import Optional
from pydantic import validator

class GuestStatsResponse(BaseModel):
    name: str
    email: str
    username: str
    mac_add: Optional[str]
    os_client: Optional[str] = None
    browser_client: Optional[str]
    device_client: Optional[str] = None
    brand_client: Optional[str] = None
    model_client: Optional[str] = None
    device_type: Optional[str] = None
    created_at: datetime
    updated_at: Optional[datetime]
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