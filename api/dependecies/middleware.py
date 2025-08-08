from fastapi import Depends, HTTPException, Header
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.future import select
from api.database.session import get_db
from api.models.models import User

# Middleware/dependency untuk validasi X-API-KEY
async def verify_api_key(x_api_key: str = Header(None), db: AsyncSession = Depends(get_db)):
    if not x_api_key:
        raise HTTPException(status_code=401, detail="X-API-KEY header is required")

    # Cek apakah token ada di DB
    result = await db.execute(select(User).where(User.remember_token == x_api_key))
    user = result.scalar_one_or_none()

    if not user:
        raise HTTPException(status_code=401, detail="Invalid X-API-KEY")

    return user  # return object user jika perlu digunakan di route

