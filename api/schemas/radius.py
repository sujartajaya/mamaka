from pydantic import BaseModel
from typing import List
from api.schemas.radcheck import RadcheckResponse
from api.schemas.radreply import RadreplyResponse

class RadiusUserResponse(BaseModel):
    username: str
    radcheck: List[RadcheckResponse]
    radreply: List[RadreplyResponse]
