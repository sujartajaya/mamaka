from telegram import Update
from telegram.ext import ContextTypes

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    message = (
        "👋 Selamat datang!\n\n"
        "Menu perintah:\n"
        "/register - Register kontak untuk dapat akses menu\n"
        "/status - Status kontak\n"
        "/traffic - Lihat pemakaian band width saat ini\n"
        "/download - Download data csv guest\n"
        "/help - Bantuan menu"
    )
    await update.message.reply_text(message)
