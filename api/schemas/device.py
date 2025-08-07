from pydantic import BaseModel, Field
from typing import Optional

class DeviceResponse(BaseModel):
    os_client: str
    browser_client: str
    device_client: str
    brand_client: str
    model_client: str

class DeviceCheck(BaseModel):
    useragent: str
    