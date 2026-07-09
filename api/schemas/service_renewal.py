from datetime import date, datetime
from decimal import Decimal
from typing import Optional
from pydantic import BaseModel, Field

# 1. Base Schema (Field utama yang wajib diisi oleh pengguna)
class ServiceRenewalBase(BaseModel):
    service_code: str = Field(..., max_length=255)
    service_name: str = Field(..., max_length=255)
    service_description: str
    service_price: Decimal = Field(..., max_digits=10, decimal_places=2)
    service_start_date: date
    service_end_date: date
    service_status: str = Field(..., max_length=255)

# 2. Create Schema (POST) -> Bersih dari ID & Timestamp karena dihandle sistem
class ServiceRenewalCreate(ServiceRenewalBase):
    pass

# 3. Update Schema (PUT/PATCH) -> Semua opsional untuk partial update
class ServiceRenewalUpdate(BaseModel):
    service_code: Optional[str] = Field(None, max_length=255)
    service_name: Optional[str] = Field(None, max_length=255)
    service_description: Optional[str] = None
    service_price: Optional[Decimal] = Field(None, max_digits=10, decimal_places=2)
    service_start_date: Optional[date] = None
    service_end_date: Optional[date] = None
    service_status: Optional[str] = Field(None, max_length=255)

# 4. Response Schema (GET) -> Menampilkan data lengkap beserta Timestamp hasil generate otomatis
class ServiceRenewalResponse(ServiceRenewalBase):
    id: int
    created_at: Optional[datetime] = None
    updated_at: Optional[datetime] = None

    class Config:
        from_attributes = True