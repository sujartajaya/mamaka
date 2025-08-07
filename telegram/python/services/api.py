import requests
from bs4 import BeautifulSoup
from config import API_BASE_URL, VERIFY_SSL
import re

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
        url = f"{API_BASE_URL}/traffic/get/{endpoint}"
        response = requests.get(url, params=params, verify=VERIFY_SSL)
        response.raise_for_status()
        return response.text  # raw HTML
    except Exception as e:
        return None

def extract_traffic_for_interface(html_text, interface_key, period_name):
    soup = BeautifulSoup(html_text, "html.parser")
    boxes = soup.find_all("div", class_="box")

    for box in boxes:
        title_tag = box.find("h3")
        if not title_tag:
            continue

        title = title_tag.text.strip().lower()
        if period_name not in title:
            continue

        # Cari gambar
        img_tag = box.find("img")
        image_url = img_tag['src'] if img_tag else ""

        # Cari angka dari tag <p>
        p_tag = box.find("p")
        raw_text = p_tag.get_text() if p_tag else ""
        numbers = re.findall(r"([\d.]+[KMG]?b)", raw_text, re.IGNORECASE)

        try:
            return {
                'image_url': image_url,
                'in_max': numbers[0],
                'in_avg': numbers[1],
                'in_cur': numbers[2],
                'out_max': numbers[3],
                'out_avg': numbers[4],
                'out_cur': numbers[5]
            }
        except IndexError:
            return None
    return None


def api_get_file(endpoint, params=None):
    try:
        url = f"{API_BASE_URL}/{endpoint}"
        response = requests.get(url, params=params, verify=VERIFY_SSL)
        response.raise_for_status()
        return response.content, None  # return file content (bytes)
    except Exception as e:
        return None, str(e)

def api_post_file(endpoint, data=None):
    try:
        url = f"{API_BASE_URL}/{endpoint}"
        response = requests.post(url, json=data, verify=VERIFY_SSL)
        response.raise_for_status()
        return response.content, None
    except Exception as e:
        return None, str(e)