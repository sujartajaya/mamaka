from fastapi import Depends, HTTPException, Header
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.future import select
from api.database.session import get_db
from api.models.models import User

# Middleware/dependency untuk validasi X-API-KEY
async def verify_api_key(
    x_api_key: str = Header(None, alias="x-api-key", convert_underscores=False),
    db: AsyncSession = Depends(get_db)
):
    # Debug: tampilkan apa yang diterima
    print("==== DEBUG HEADER X-API-KEY ====", x_api_key)

    if not x_api_key:
        raise HTTPException(status_code=401, detail="X-API-KEY header is required")

    # Query DB
    result = await db.execute(select(User).where(User.remember_token == x_api_key))
    user = result.scalar_one_or_none()

    # Debug: tampilkan hasil query DB
    print("==== DEBUG DB USER ====", user)

    # Kalau user tidak ditemukan
    if not user:
        # Cek semua token yang ada di DB (debug saja, jangan dipakai di production)
        all_tokens = await db.execute(select(User.remember_token))
        print("==== DEBUG ALL TOKENS IN DB ====", all_tokens.scalars().all())
        raise HTTPException(status_code=401, detail="Invalid X-API-KEY")

    return user


# from fastapi import Request
# from fastapi.responses import JSONResponse
# from starlette.middleware.base import BaseHTTPMiddleware
# from api.utils.security import verify_token

# class JWTAuthMiddleware(BaseHTTPMiddleware):
#     async def dispatch(self, request: Request, call_next):
#         # skip auth untuk endpoint tertentu
#         if request.url.path in ["/api/users/login", "/api/users/register", "/open-endpoint","/docs", "/openapi.json","/api/device/client","/api/guests/export-csv","/api/client-devices/headers", "/api/client-devices/"]:
#             return await call_next(request)

#         auth_header = request.headers.get("Authorization")
#         if not auth_header or not auth_header.startswith("Bearer "):
#             return JSONResponse(
#                 status_code=401,
#                 content={"error": True, "msg": "Unauthorized"}
#             )

#         token = auth_header.split(" ")[1]
#         payload = verify_token(token)
#         if not payload:
#             return JSONResponse(
#                 status_code=401,
#                 content={"error": True, "msg": "Invalid or expired token"}
#             )

#         # simpan payload user di request.state
#         request.state.user = payload
#         return await call_next(request)


import re
from fastapi import Request
from fastapi.responses import JSONResponse
from starlette.middleware.base import BaseHTTPMiddleware
from api.utils.security import verify_token


class JWTAuthMiddleware(BaseHTTPMiddleware):
    async def dispatch(self, request: Request, call_next):
        # pola regex untuk bypass
        open_patterns = [
            r"^/api/users/login$",
            r"^/api/users/register$",
            r"^/open-endpoint$",
            r"^/docs$",
            r"^/openapi.json$",
            r"^/api/device/client$",
            r"^/api/guests/export-csv$",
            r"^/api/client-devices.*$",   # semua path mulai dengan /api/client-devices
        ]

        path = request.url.path
        if any(re.match(pattern, path) for pattern in open_patterns):
            return await call_next(request)

        # cek header Authorization
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

        request.state.user = payload
        return await call_next(request)



from starlette.middleware.base import BaseHTTPMiddleware
from starlette.requests import Request
from starlette.responses import Response
from api.utils.mikrotik_async import MikroTikAsyncClient
import os


class MikroTikMiddleware(BaseHTTPMiddleware):
    def __init__(self, app):
        super().__init__(app)
        self.host = os.getenv("MIKROTIK_HOST", "192.168.88.1")
        self.username = os.getenv("MIKROTIK_USERNAME", "admin")
        self.password = os.getenv("MIKROTIK_PASSWORD", "admin123")
        self.port = int(os.getenv("MIKROTIK_PORT", 8729))
        self.use_ssl = os.getenv("MIKROTIK_USE_SSL", "True").lower() == "true"

    async def dispatch(self, request: Request, call_next):
        mikrotik_client = MikroTikAsyncClient(
            host=self.host,
            username=self.username,
            password=self.password,
            port=self.port,
            use_ssl=self.use_ssl
        )

        await mikrotik_client.connect()
        request.state.mikrotik = mikrotik_client.api

        try:
            response = await call_next(request)
        finally:
            await mikrotik_client.close()

        return response

