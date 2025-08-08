from fastapi import APIRouter
from api.routers import device, guest, user

api_router = APIRouter()
api_router.include_router(device.router, prefix="/device", tags=["Device Client"])
api_router.include_router(guest.router, prefix="/guests", tags=["Guests"])
api_router.include_router(user.router, prefix="/users", tags=["Users"])
