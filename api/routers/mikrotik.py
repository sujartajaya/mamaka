from fastapi import APIRouter, Request, HTTPException, Query, Depends
from pydantic import BaseModel, constr
from typing import Optional, Literal
# from api.models.models import User
from api.utils.security import role_required
import asyncio

# # router = APIRouter(prefix="/mikrotik/hotspot/ip-binding", tags=["Mikrotik - IP Binding"])
# router = APIRouter()


# # Schema Pydantic untuk validasi
# class IPBindingCreate(BaseModel):
#     mac_address: constr(regex=r"^([0-9A-Fa-f]{2}:){5}([0-9A-Fa-f]{2})$")  # format MAC
#     address: Optional[str] = None  # IP optional
#     type: Literal["bypassed", "blocked", "regular"] = "bypassed"
#     comment: Optional[str] = ""


# class IPBindingEdit(BaseModel):
#     address: Optional[str] = None
#     type: Optional[Literal["bypassed", "blocked", "regular"]] = None
#     comment: Optional[str] = None


# @router.get("/hotspot/ip-binding/list")
# async def list_ip_bindings(request: Request,user=Depends(role_required(["admin"]))):
#     """Ambil semua IP/MAC Binding di hotspot"""
#     api = request.state.mikrotik
#     try:
#         bindings = list(api("/ip/hotspot/ip-binding/print"))
#         return {"bindings": bindings}
#     except Exception as e:
#         raise HTTPException(status_code=500, detail=str(e))


# @router.post("/hotspot/ip-binding/add")
# async def add_ip_binding(request: Request, body: IPBindingCreate, user=Depends(role_required(["admin"]))):
#     """Tambah MAC/IP Binding baru"""
#     api = request.state.mikrotik
#     try:
#         result = api("/ip/hotspot/ip-binding/add", **{
#             "mac-address": body.mac_address,
#             **({"address": body.address} if body.address else {}),
#             "type": body.type,
#             "comment": body.comment or ""
#         })
#         return {"status": "success", "result": list(result)}
#     except Exception as e:
#         raise HTTPException(status_code=500, detail=str(e))


# @router.put("/hotspot/ip-binding/{binding_id}")
# async def edit_ip_binding(request: Request, binding_id: str, body: IPBindingEdit, user=Depends(role_required(["admin"]))):
#     """Edit MAC/IP Binding berdasarkan .id"""
#     api = request.state.mikrotik
#     try:
#         params = {".id": binding_id}
#         if body.address:
#             params["address"] = body.address
#         if body.type:
#             params["type"] = body.type
#         if body.comment is not None:
#             params["comment"] = body.comment

#         result = api("/ip/hotspot/ip-binding/set", **params)
#         return {"status": "success", "result": list(result)}
#     except Exception as e:
#         raise HTTPException(status_code=500, detail=str(e))


# @router.delete("/hotspot/ip-binding/delete/{binding_id}")
# async def delete_ip_binding(request: Request, binding_id: str, user=Depends(role_required(["admin"]))):
#     """Hapus MAC/IP Binding berdasarkan .id"""
#     api = request.state.mikrotik
#     try:
#         result = api("/ip/hotspot/ip-binding/remove", **{".id": binding_id})
#         return {"status": "success", "result": list(result)}
#     except Exception as e:
#         raise HTTPException(status_code=500, detail=str(e))


# @router.get("/interfaces")
# async def get_interfaces(request: Request, user=Depends(role_required(["admin"]))):
#     api = request.state.mikrotik
#     interfaces = list(api('/interface/print'))
#     return {"interfaces": interfaces}


# @router.get("/hotspot/user/active")
# async def get_interfaces(request: Request, user=Depends(role_required(["admin"]))):
#     api = request.state.mikrotik
#     useractive = list(api('/ip/hotspot/active/print'))
#     return {"users": useractive}




router = APIRouter()

# -----------------------
# Schema untuk validasi
# -----------------------
class IPBindingCreate(BaseModel):
    mac_address: constr(regex=r"^([0-9A-Fa-f]{2}:){5}([0-9A-Fa-f]{2})$")
    address: Optional[str] = None
    type: Literal["bypassed", "blocked", "regular"] = "bypassed"
    comment: Optional[str] = ""


class IPBindingEdit(BaseModel):
    address: Optional[str] = None
    type: Optional[Literal["bypassed", "blocked", "regular"]] = None
    comment: Optional[str] = None


# -----------------------
# Helper async wrapper
# -----------------------
async def mikrotik_cmd(api, path: str, **kwargs):
    """Jalankan perintah Mikrotik di thread terpisah"""
    try:
        return await asyncio.to_thread(lambda: list(api(path, **kwargs)))
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


# -----------------------
# Routes
# -----------------------
@router.get("/hotspot/ip-binding/list")
async def list_ip_bindings(request: Request, user=Depends(role_required(["admin"]))):
    """Ambil semua IP/MAC Binding di hotspot"""
    api = request.state.mikrotik
    bindings = await mikrotik_cmd(api, "/ip/hotspot/ip-binding/print")
    return {"bindings": bindings}


@router.post("/hotspot/ip-binding/add")
async def add_ip_binding(request: Request, body: IPBindingCreate, user=Depends(role_required(["admin"]))):
    """Tambah MAC/IP Binding baru"""
    api = request.state.mikrotik
    params = {
        "mac-address": body.mac_address,
        "type": body.type,
        "comment": body.comment or ""
    }
    if body.address:
        params["address"] = body.address

    result = await mikrotik_cmd(api, "/ip/hotspot/ip-binding/add", **params)
    return {"status": "success", "result": result}


@router.put("/hotspot/ip-binding/edit/{binding_id}")
async def edit_ip_binding(request: Request, binding_id: str, body: IPBindingEdit, user=Depends(role_required(["admin"]))):
    """Edit MAC/IP Binding berdasarkan .id"""
    api = request.state.mikrotik
    params = {".id": binding_id}
    if body.address:
        params["address"] = body.address
    if body.type:
        params["type"] = body.type
    if body.comment is not None:
        params["comment"] = body.comment

    result = await mikrotik_cmd(api, "/ip/hotspot/ip-binding/set", **params)
    return {"status": "success", "result": result}


@router.delete("/hotspot/ip-binding/delete/{binding_id}")
async def delete_ip_binding(request: Request, binding_id: str, user=Depends(role_required(["admin"]))):
    """Hapus MAC/IP Binding berdasarkan .id"""
    api = request.state.mikrotik
    result = await mikrotik_cmd(api, "/ip/hotspot/ip-binding/remove", **{".id": binding_id})
    return {"status": "success", "result": result}


@router.get("/interfaces")
async def get_interfaces(request: Request, user=Depends(role_required(["admin"]))):
    api = request.state.mikrotik
    interfaces = await mikrotik_cmd(api, "/interface/print")
    return {"interfaces": interfaces}


from fastapi import APIRouter, HTTPException, Request, Depends
from fastapi.responses import HTMLResponse
import pycurl
from io import BytesIO
from typing import Literal
from api.utils.security import role_required
import os


ROUTER_OS_URL = os.getenv("MIKROTIK_HOST", "192.168.1.2")


# -----------------------
# Helper Function dengan PycURL
# -----------------------
def fetch_mikrotik_html_curl(interface: str) -> str:
    """Fetch HTML content using pycurl"""
    base_url = f"https://{ROUTER_OS_URL}/graphs/iface/"
    url = f"{base_url}{interface}"
    
    buffer = BytesIO()
    c = pycurl.Curl()
    
    try:
        c.setopt(c.URL, url)
        c.setopt(c.WRITEDATA, buffer)
        c.setopt(c.FOLLOWLOCATION, True)
        c.setopt(c.TIMEOUT, 10)
        
        c.perform()
        status_code = c.getinfo(c.RESPONSE_CODE)
        
        if status_code != 200:
            raise HTTPException(
                status_code=502,
                detail=f"MikroTik returned status code: {status_code}"
            )
            
        return buffer.getvalue().decode('utf-8')
        
    except pycurl.error as e:
        raise HTTPException(
            status_code=500,
            detail=f"Curl error: {str(e)}"
        )
    finally:
        c.close()

# -----------------------
# Routes
# -----------------------
@router.get("/traffic/fetch-html/{interface}", response_class=HTMLResponse)
async def fetch_html(
    interface: str,
    request: Request,
    user=Depends(role_required(["admin"]))
):
    """
    Fetch HTML content for specific MikroTik interface
    Example interfaces: 'ether1', 'VLAN-50', 'bridge-vlan-20'
    """
    html_content = fetch_mikrotik_html_curl(interface)
    return HTMLResponse(content=html_content)

@router.get("/traffic/wan", response_class=HTMLResponse)
async def wan_traffic(request: Request, user=Depends(role_required(["admin"]))):
    """Get WAN traffic page"""
    html_content = fetch_mikrotik_html_curl("ether1")
    return HTMLResponse(content=html_content)

@router.get("/traffic/guest", response_class=HTMLResponse)
async def guest_traffic(request: Request, user=Depends(role_required(["admin"]))):
    """Get Guest traffic page"""
    html_content = fetch_mikrotik_html_curl("VLAN-50")
    return HTMLResponse(content=html_content)

@router.get("/traffic/boh", response_class=HTMLResponse)
async def boh_traffic(request: Request, user=Depends(role_required(["admin"]))):
    """Get BOH traffic page"""
    html_content = fetch_mikrotik_html_curl("bridge-vlan-20")
    return HTMLResponse(content=html_content)