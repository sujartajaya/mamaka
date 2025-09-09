# api/schemas/radcheck.py
from pydantic import BaseModel, constr
from typing import Optional

class RadreplyCreate(BaseModel):
    username: constr(min_length=1, max_length=64)
    attribute: constr(min_length=1, max_length=64)
    op: constr(min_length=1, max_length=2) = "=="
    value: constr(min_length=1, max_length=253)

    class Config:
        orm_mode = True

class RadreplyResponse(BaseModel):
    id: Optional[int] = None
    username: Optional[str] = None
    attribute: Optional[str] = None
    op: Optional[str] = None
    value: Optional[str] = None

    class Config:
        orm_mode = True