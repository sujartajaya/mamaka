from pydantic import BaseModel
from typing import Optional
from datetime import datetime
from typing import List, Optional
import uuid

# class ClientDeviceBase(BaseModel):
#     username: str
#     mac_add: Optional[str] = None
#     os_client: Optional[str] = None
#     browser_client: Optional[str] = None
#     device_client: Optional[str] = None
#     brand_client: Optional[str] = None
#     model_client: Optional[str] = None
#     device_type: Optional[str] = None

class ClientDeviceBase(BaseModel):
    id: int
    username: str
    mac_add: Optional[str]
    os_client: Optional[str]
    browser_client: Optional[str]
    device_client: Optional[str]
    brand_client: Optional[str]
    model_client: Optional[str]
    device_type: Optional[str]

    class Config:
        orm_mode = True

class ClientDeviceCreate(ClientDeviceBase):
    pass

class ClientDeviceUpdate(ClientDeviceBase):
    pass

class ClientDeviceResponse(ClientDeviceBase):
    id: int
    created_at: Optional[datetime] = None
    updated_at: Optional[datetime] = None

    class Config:
        orm_mode = True



class GuestBase(BaseModel):
    id: uuid.UUID
    name: str
    email: str
    username: str
    password: str

    class Config:
        orm_mode = True



class GuestWithDevices(GuestBase):
    client_devices: List[ClientDeviceBase] = []

