from fastapi import FastAPI, APIRouter
from user_agents import parse
from api.schemas.device import DeviceCheck, DeviceResponse

router = APIRouter()


@router.post("/client", response_model=DeviceResponse)
def device_client_check(useragent: DeviceCheck):
    ua = parse(useragent.useragent)
    print(f"Data ua : \n{ua}")
    return {
        "os_client": ua.os.family + " " + ua.os.version_string,
        "browser_client": ua.browser.family + " " + ua.browser.version_string,
        "device_client": ua.device.family,
        "brand_client": ua.device.brand,
        "model_client": ua.device.model
    }
