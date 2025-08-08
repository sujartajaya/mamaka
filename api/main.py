from __future__ import annotations
from fastapi import FastAPI
from api.database.session import engine, Base
from api.routers.api import api_router
from fastapi.middleware.cors import CORSMiddleware

app = FastAPI(title="FastAPI MySQL Docker", version="0.1.0")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Bisa diubah sesuai domain asal
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# @app.on_event("startup")
# async def startup():
#     # Create database tables
#     async with engine.begin() as conn:
#         await conn.run_sync(Base.metadata.create_all)

app.include_router(api_router, prefix="/api")