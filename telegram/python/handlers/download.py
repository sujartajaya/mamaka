from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import ContextTypes
import datetime
from io import BytesIO
from services.api import api_get_file, api_post_file

DOWNLOAD_URLS = {
    'macbinding': 'telegram/csv/macbinding',
    'useractive': 'telegram/csv/useractive',
    'userguest': 'telegram/csv/email'  # ini khusus POST dengan tanggal
}

def validate_date(date_str):
    try:
        return datetime.datetime.strptime(date_str, "%Y-%m-%d")
    except ValueError:
        return None

async def download(update: Update, context: ContextTypes.DEFAULT_TYPE):
    keyboard = [
        [InlineKeyboardButton("Download MAC Binding", callback_data='download_macbinding')],
        [InlineKeyboardButton("Download User Active", callback_data='download_useractive')],
        [InlineKeyboardButton("Download User Guest", callback_data='download_userguest')]
    ]
    await update.message.reply_text("Pilih jenis data yang ingin didownload:", reply_markup=InlineKeyboardMarkup(keyboard))

async def download_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    option = query.data.replace('download_', '')

    message = query.message  # fallback aman

    if option == 'userguest':
        context.user_data['download'] = 'userguest'
        context.user_data['download_step'] = 'start_date'
        await message.reply_text("Masukkan tanggal awal (format YYYY-MM-DD):")
    else:
        endpoint = DOWNLOAD_URLS.get(option)
        content, error = api_get_file(endpoint)

        if error:
            await message.reply_text(f"Gagal download data: {error}")
        else:
            bio = BytesIO(content)
            bio.name = f"{option}.csv"
            await message.reply_document(document=bio)

async def download_guest_date(update: Update, context: ContextTypes.DEFAULT_TYPE):
    step = context.user_data.get('download_step')
    text = update.message.text.strip()
    message = update.message  # aman digunakan karena berasal dari MessageHandler

    if step == 'start_date':
        start_date = validate_date(text)
        if not start_date:
            await message.reply_text("Format tanggal tidak valid. Gunakan YYYY-MM-DD")
            return
        context.user_data['start_date'] = text
        context.user_data['download_step'] = 'end_date'
        await message.reply_text("Masukkan tanggal akhir (format YYYY-MM-DD):")

    elif step == 'end_date':
        end_date = validate_date(text)
        start_date = validate_date(context.user_data.get('start_date'))

        if not end_date:
            await message.reply_text("Format tanggal tidak valid. Gunakan YYYY-MM-DD")
            return

        if start_date > end_date:
            await message.reply_text("Tanggal awal tidak boleh lebih besar dari tanggal akhir.")
            return

        payload = {
            'start_date': context.user_data['start_date'],
            'end_date': text
        }

        content, error = api_post_file(DOWNLOAD_URLS['userguest'], payload)

        if error:
            await message.reply_text(f"Gagal download data: {error}")
        else:
            bio = BytesIO(content)
            bio.name = "user_guest.csv"
            await message.reply_document(document=bio)

        # Bersihkan session
        context.user_data.pop('download_step', None)
        context.user_data.pop('start_date', None)
        context.user_data.pop('download', None)
