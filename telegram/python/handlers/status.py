from telegram import Update
from telegram.ext import ContextTypes
from services.api import api_get

async def status(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    result = api_get("telegram/user/"+str(user_id))
    if result['exist']  and result['user']['verified'] == '0':
        await update.message.reply_text(f"📌 Kontak anda masih dalam proses review oleh admin!")
    else:
        await update.message.reply_text(f"✅ Kontak anda sudah direview dan sebagai {result['user']['role']}")
