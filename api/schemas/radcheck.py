# api/schemas/radcheck.py
from pydantic import BaseModel, constr

class RadcheckCreate(BaseModel):
    username: constr(min_length=1, max_length=64)
    attribute: constr(min_length=1, max_length=64)
    op: constr(min_length=1, max_length=2) = "=="
    value: constr(min_length=1, max_length=253)

    class Config:
        orm_mode = True
