from fastapi import APIRouter
from api.routers import device

api_router = APIRouter()
api_router.include_router(device.router, prefix="/device", tags=["Device Client"])
