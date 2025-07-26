from telegram import Update
from telegram.ext import ContextTypes
from services.api import register_user

async def register(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user

    user_data = {
        "telegram_id": user.id,
        "first_name": user.first_name or "",
        "last_name": user.last_name or "",
        "username": user.username or "",
        "phone": ""  # jika belum bisa diset dari Telegram, kosongkan
        # "role": "user",  # default bisa diubah nanti
        # "verified": 0,
        # "verified_at": None  # auto set di server
    }

    result = register_user(user_data)

    if "error" in result:
        await update.message.reply_text(f"❌ Gagal registrasi:\n{result['error']}")
    else:
        await update.message.reply_text("✅ Registrasi berhasil!")
