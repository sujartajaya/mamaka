from __future__ import annotations
from fastapi import FastAPI
from api.database.session import engine, Base
from api.routers.api import api_router
from fastapi.middleware.cors import CORSMiddleware
from api.dependencies.middleware import JWTAuthMiddleware
from api.config.system import settings
from api.dependencies.middleware import MikroTikMiddleware
# from api.routers import mikrotik

app = FastAPI(title="FastAPI MySQL Docker", version="0.1.0")

app.add_middleware(MikroTikMiddleware)

# Tambahkan middleware MikroTik
# app.add_middleware(
#     MikroTikMiddleware,
#     host=settings.MIKROTIK_HOST,
#     username=settings.MIKROTIK_USERNAME,
#     password=settings.MIKROTIK_PASSWORD,
#     port=settings.MIKROTIK_PORT,
#     use_ssl=settings.MIKROTIK_SSL
# )


# origins = [
#     "http://localhost:5173",  # React dev server
#     "http://127.0.0.1:5173",
#     "*"  # kalau mau izinkan semua origin (hati-hati di production)
# ]

# app.add_middleware(
#     CORSMiddleware,
#     allow_origins=origins,  # Bisa diubah sesuai domain asal
#     allow_credentials=True,
#     allow_methods=["*"],
#     allow_headers=["*"],
# )


app.add_middleware(JWTAuthMiddleware)

# @app.on_event("startup")
# async def startup():
#     # Create database tables
#     async with engine.begin() as conn:
#         await conn.run_sync(Base.metadata.create_all)

app.include_router(api_router, prefix="/api")