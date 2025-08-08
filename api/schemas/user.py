from pydantic import BaseModel, EmailStr, constr
from typing import Optional, Literal
from datetime import datetime

class UserBase(BaseModel):
    name: str
    email: EmailStr
    username: constr(min_length=3, max_length=50)
    type: Literal['admin', 'operator', 'user'] = 'user'

class UserCreate(UserBase):
    password: constr(min_length=6)

class UserUpdate(BaseModel):
    name: Optional[str]
    email: Optional[EmailStr]
    username: Optional[constr(min_length=3, max_length=50)]
    type: Optional[Literal['admin', 'operator', 'user']]
    password: Optional[constr(min_length=6)]

class UserOut(UserBase):
    id: int
    created_at: Optional[datetime] = None
    updated_at: Optional[datetime] = None

    class Config:
        orm_mode = True