from fastapi import APIRouter
from api.routers import device, guest, user, mikrotik, hotspot, client_device, service_renewal

api_router = APIRouter()
api_router.include_router(device.router, prefix="/device", tags=["Useragent Device"])
api_router.include_router(client_device.router, prefix="/client-devices", tags=["Client Divices"])
api_router.include_router(guest.router, prefix="/guests", tags=["Guests"])
api_router.include_router(user.router, prefix="/users", tags=["Users"])
api_router.include_router(mikrotik.router, prefix="/routeros", tags=["Router OS"])
api_router.include_router(hotspot.router, prefix="/hotspot", tags=["Hotspot"])
api_router.include_router(service_renewal.router, prefix="/service-renewals", tags=["Service Renewals"])