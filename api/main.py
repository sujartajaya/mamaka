from __future__ import annotations
from fastapi import FastAPI
from api.database.session import engine, Base
from api.routers.api import api_router
from fastapi.middleware.cors import CORSMiddleware
from api.dependencies.middleware import JWTAuthMiddleware

app = FastAPI(title="FastAPI MySQL Docker", version="0.1.0")

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