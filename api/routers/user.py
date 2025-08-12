from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.future import select
from api.models.models import User
from api.schemas.user import UserCreate, UserUpdate, UserOut
from api.utils.security import hash_password, create_unique_token, verify_password
from api.database.session import get_db
from datetime import datetime
from api.utils.timezone import now

router = APIRouter()

# CREATE USER
# @router.post("/", response_model=UserOut)
# async def create_user(user: UserCreate, db: AsyncSession = Depends(get_db)):
#     # cek email unik
#     q_email = await db.execute(select(User).where(User.email == user.email))
#     if q_email.scalar_one_or_none():
#         raise HTTPException(status_code=400, detail="Email already registered")

#     # cek username unik
#     q_username = await db.execute(select(User).where(User.username == user.username))
#     if q_username.scalar_one_or_none():
#         raise HTTPException(status_code=400, detail="Username already taken")

#     new_user = User(
#         name=user.name,
#         email=user.email,
#         username=user.username,
#         type=user.type,
#         password=hash_password(user.password),
#         created_at=now(),
#         updated_at=now()
#     )
#     db.add(new_user)
#     await db.commit()
#     await db.refresh(new_user)
#     return new_user

@router.post("/")
async def register(user: UserCreate, db: AsyncSession = Depends(get_db)):
    # Cek username
    result = await db.execute(select(User).where(User.username == user.username))
    if result.scalar_one_or_none():
        raise HTTPException(status_code=400, detail="Username sudah digunakan")

    # Cek email
    result = await db.execute(select(User).where(User.email == user.email))
    if result.scalar_one_or_none():
        raise HTTPException(status_code=400, detail="Email sudah digunakan")

    # Generate token unik (async)
    token = await create_unique_token(db)

    new_user = User(
        name=user.name,
        username=user.username,
        email=user.email,
        type=user.type,
        password=hash_password(user.password),
        remember_token=token,
        created_at=now(),
        updated_at=now()
    )

    db.add(new_user)
    await db.commit()
    await db.refresh(new_user)

    return {
        "message": "Success",
        "user": {
            "id": new_user.id,
            "username": new_user.username,
            "email": new_user.email,
            "type": new_user.type,
            "remember_token": new_user.remember_token,
            "created_at": new_user.created_at,
            "updated_at": new_user.updated_at
        }
    }

from api.utils.security import role_required

# READ ALL USERS
@router.get("/")
async def get_users(
    db: AsyncSession = Depends(get_db),
    user=Depends(role_required(["admin", "operator", "user"]))
):
    result = await db.execute(select(User))
    users = result.scalars().all()
    print(f"Ini data usernya:\n{users}")
    return users

# READ USER BY ID
@router.get("/{user_id}", response_model=UserOut)
async def get_user(user_id: int, db: AsyncSession = Depends(get_db)):
    result = await db.execute(select(User).where(User.id == user_id))
    user = result.scalar_one_or_none()
    if not user:
        raise HTTPException(status_code=404, detail="User not found")
    return user

# UPDATE USER
@router.put("/{user_id}", response_model=UserOut)
async def update_user(user_id: int, user_update: UserUpdate, db: AsyncSession = Depends(get_db)):
    result = await db.execute(select(User).where(User.id == user_id))
    user = result.scalar_one_or_none()
    if not user:
        raise HTTPException(status_code=404, detail="User not found")

    if user_update.name is not None:
        user.name = user_update.name
    if user_update.email is not None:
        user.email = user_update.email
    if user_update.username is not None:
        user.username = user_update.username
    if user_update.type is not None:
        user.type = user_update.type
    if user_update.password is not None:
        user.password = hash_password(user_update.password)
    
    user.updated_at=now()

    await db.commit()
    await db.refresh(user)
    return user

# DELETE USER
@router.delete("/{user_id}")
async def delete_user(user_id: int, db: AsyncSession = Depends(get_db)):
    result = await db.execute(select(User).where(User.id == user_id))
    user = result.scalar_one_or_none()
    if not user:
        raise HTTPException(status_code=404, detail="User not found")
    await db.delete(user)
    await db.commit()
    return {"message": "User deleted successfully"}


from api.utils.security import create_access_token, ACCESS_TOKEN_EXPIRE_MINUTES
from datetime import timedelta

@router.post("/login", response_model=dict)
async def login_user(login_data: dict, db: AsyncSession = Depends(get_db)):
    username = login_data.get("username")
    password = login_data.get("password")
    # print(f"DATA :\nUsername = {username}\nPassword = {password}")
    if not username or not password:
        raise HTTPException(status_code=400, detail="Username dan password wajib diisi")

    # Cari user berdasarkan username
    result = await db.execute(select(User).where(User.username == username))
    user = result.scalar_one_or_none()

    if not user:
        raise HTTPException(status_code=401, detail="Username atau password salah")

    # Verifikasi password
    if not verify_password(password, user.password):
        raise HTTPException(status_code=401, detail="Username atau password salah")

    # Ambil token dari remember_token yang sudah ada
    if not user.remember_token:
        """ Membuat token baru """
        token = await create_unique_token(db)
        user.remember_token = token
        user.updated_at=now()
        await db.commit()
        await db.refresh(user)
    
        # raise HTTPException(status_code=500, detail="Token belum dibuat, silakan register ulang")
    to_encode = {
        "id": user.id,
        "username": user.username,
        "role": user.type
    }

    token = create_access_token(to_encode, expires_delta=timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES))

    return {"remember_token": user.remember_token,"token_type": "Bearer", "token": token}



