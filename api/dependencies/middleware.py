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


from fastapi import Request
from fastapi.responses import JSONResponse
from starlette.middleware.base import BaseHTTPMiddleware
from api.utils.security import verify_token

class JWTAuthMiddleware(BaseHTTPMiddleware):
    async def dispatch(self, request: Request, call_next):
        # skip auth untuk endpoint tertentu
        if request.url.path in ["/api/users/login", "/api/users/register", "/open-endpoint","/docs", "/openapi.json","/api/device/client","/api/guests/export-csv"]:
            return await call_next(request)

        auth_header = request.headers.get("Authorization")
        if not auth_header or not auth_header.startswith("Bearer "):
            return JSONResponse(
                status_code=401,
                content={"error": True, "msg": "Unauthorized"}
            )

        token = auth_header.split(" ")[1]
        payload = verify_token(token)
        if not payload:
            return JSONResponse(
                status_code=401,
                content={"error": True, "msg": "Invalid or expired token"}
            )

        # simpan payload user di request.state
        request.state.user = payload
        return await call_next(request)
