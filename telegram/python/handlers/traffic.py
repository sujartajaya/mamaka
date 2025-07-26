from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import ContextTypes, CallbackQueryHandler
from services.api import get_traffic_info

TRAFFIC_TYPES = ['wan', 'lan', 'guest', 'boh']
TRAFFIC_PERIODS = ['daily', 'weekly', 'monthly', 'yearly']

async def traffic(update: Update, context: ContextTypes.DEFAULT_TYPE):
    keyboard = [
        [InlineKeyboardButton(f"Traffic {t.upper()}", callback_data=f"traffic_{t}")]
        for t in TRAFFIC_TYPES
    ]
    reply_markup = InlineKeyboardMarkup(keyboard)
    await update.message.reply_text("📡 Pilih jenis traffic:", reply_markup=reply_markup)

async def traffic_type_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    traffic_type = query.data.split("_")[1]

    keyboard = [
        [InlineKeyboardButton(p.capitalize(), callback_data=f"{traffic_type}_{p}")]
        for p in TRAFFIC_PERIODS
    ]
    reply_markup = InlineKeyboardMarkup(keyboard)
    await query.edit_message_text(
        text=f"📊 Pilih periode untuk traffic {traffic_type.upper()}:", reply_markup=reply_markup
    )

async def traffic_period_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    traffic_type, period = query.data.split("_")

    msg = await query.edit_message_text(f"🔄 Mengambil data {traffic_type.upper()} - {period.capitalize()}...")

    result = get_traffic_info(traffic_type, period)
    if "error" in result:
        await msg.edit_text(f"❌ Gagal mengambil data:\n{result['error']}")
        return

    image_url = result["image_url"]
    caption = result["text"]

    await context.bot.send_photo(
        chat_id=update.effective_chat.id,
        photo=image_url,
        caption=caption
    )
