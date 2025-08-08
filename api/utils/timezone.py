from datetime import datetime
from zoneinfo import ZoneInfo

# Set global timezone
LOCAL_TZ = ZoneInfo("Asia/Makassar")

def now():
    """Return waktu sekarang sesuai timezone lokal."""
    return datetime.now(LOCAL_TZ)
