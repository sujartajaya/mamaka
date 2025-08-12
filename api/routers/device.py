from fastapi import FastAPI, APIRouter
from user_agents import parse
from api.schemas.device import DeviceCheck, DeviceResponse

router = APIRouter()


@router.post("/client", response_model=DeviceResponse)
def device_client_check(useragent: DeviceCheck):
    ua = parse(useragent.useragent)
    # print(f"Data ua : \n{ua}")
    if ua.is_mobile:
        device_type = "Mobile"
    elif ua.is_tablet:
        device_type = "Tablet"
    elif ua.is_pc:
        device_type = "Desktop"
    elif ua.is_bot:
        device_type = "Bot"
    else:
        device_type = "Unknown"

    return {
        "os_client": ua.os.family + " " + ua.os.version_string,
        "browser_client": ua.browser.family + " " + ua.browser.version_string,
        "device_client": ua.device.family,
        "brand_client": ua.device.brand,
        "model_client": ua.device.model,
        "device_type": device_type
    }

