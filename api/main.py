from __future__ import annotations
from fastapi import FastAPI
from api.database.session import engine, Base
from api.routers.api import api_router

app = FastAPI(title="FastAPI MySQL Docker", version="0.1.0")

# @app.on_event("startup")
# async def startup():
#     # Create database tables
#     async with engine.begin() as conn:
#         await conn.run_sync(Base.metadata.create_all)

app.include_router(api_router, prefix="/api")