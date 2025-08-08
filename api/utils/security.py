from passlib.hash import bcrypt
from api.models.models import User
import random
import string
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select
from api.database.session import get_db

def hash_password(password: str) -> str:
    hashed = bcrypt.using(rounds=10).hash(password)
    # Ganti prefix $2b$ jadi $2y$ agar identik dengan Laravel
    return hashed.replace("$2b$", "$2y$")

def verify_password(password: str, hashed: str) -> bool:
    # Laravel akan pakai bcrypt verify, jadi di Python ini untuk cek internal saja
    return bcrypt.verify(password, hashed.replace("$2y$", "$2b$"))

async def create_unique_token(db) -> str:
    while True:
        # Bagian A: angka acak 6 digit
        part_a = ''.join(random.choices(string.digits, k=8))
        # Bagian B: huruf besar saja
        part_b = ''.join(random.choices(string.ascii_uppercase, k=8))
        # Bagian C: huruf besar + angka
        part_c = ''.join(random.choices(string.ascii_uppercase + string.digits, k=8))
        token = f"{part_a}-{part_b}-{part_c}"
        # Cek apakah token sudah ada di database
        # Query async untuk cek token
        result = await db.execute(
            select(User).where(User.remember_token == token)
        )
        existing_user = result.scalar_one_or_none()

        if not existing_user:
            return token
