from telegram import Update
from telegram.ext import ContextTypes

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    message = (
        "👋 Selamat datang!\n\n"
        "Menu perintah:\n"
        "/register\n"
        "/status\n"
        "/traffic\n"
        "/download\n"
        "/help"
    )
    await update.message.reply_text(message)
