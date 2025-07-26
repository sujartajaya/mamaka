import requests
from bs4 import BeautifulSoup
from config import API_BASE_URL, VERIFY_SSL

def clean_text(value):
    return value if value else ""

def register_user(user_data: dict):
    """
    Mengirim data user ke endpoint /register (POST)
    """
    url = f"{API_BASE_URL}/telegram/user"
    
    # Normalisasi dan validasi
    data = {
        "telegram_id": user_data.get("telegram_id"),
        "first_name": clean_text(user_data.get("first_name")),
        "last_name": clean_text(user_data.get("last_name")),
        "username": clean_text(user_data.get("username")),
        "phone": clean_text(user_data.get("phone")),
        # "role": user_data.get("role", "user"),  # default 'user'
        # "verified": int(user_data.get("verified", 0)),  # default 0
        # "verified_at": user_data.get("verified_at") or datetime.utcnow().isoformat()
    }

    try:
        response = requests.post(url, json=data, verify=VERIFY_SSL)
        response.raise_for_status()
        return response.json()
    except Exception as e:
        return {"error": str(e)}

def api_get(endpoint, params=None):
    try:
        url = f"{API_BASE_URL}/{endpoint}"
        response = requests.get(url, params=params, verify=VERIFY_SSL)
        response.raise_for_status()
        return response.json()
    except Exception as e:
        return {'error': str(e)}

def api_get_html(endpoint, params=None):
    try:
        url = f"{API_BASE_URL}/{endpoint}"
        response = requests.get(url, params=params, verify=VERIFY_SSL)
        response.raise_for_status()
        return response.text  # raw HTML
    except Exception as e:
        return None

def extract_traffic_image_url(html_text):
    soup = BeautifulSoup(html_text, "html.parser")
    # Misal ambil image pertama dari tag <img>
    img_tag = soup.find("img")
    if img_tag and "src" in img_tag.attrs:
        return img_tag["src"]
    return None
