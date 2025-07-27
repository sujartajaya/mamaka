from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import ContextTypes
from services.api import api_get_html, extract_traffic_for_interface, api_get
from config import API_BASE_URL, ROUTER_BASE_URL

# Mapping interface ke nama internal
INTERFACE_MAPPING = {
    'wan': 'ether1',
    'lan': 'ether2',
    'guest': 'VLAN-50',
    'boh': 'VLAN-20'
}

PERIODS = ['daily', 'weekly', 'monthly', 'yearly']

# Command /traffic ➜ pilih interface
async def traffic(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    result = api_get("telegram/user/"+str(user_id))
    if not result['exist']:
        await update.message.reply_text(f"🗒️ Silakan /register dulu!")
    elif result['exist']  and result['user']['verified'] == '0':
        await update.message.reply_text(f"📝 Kontak anda masih dalam proses review oleh admin!")
    else:
        keyboard = [
            [InlineKeyboardButton(f"Traffic {key.upper()}", callback_data=f"traffic_{key}")]
            for key, name in INTERFACE_MAPPING.items()
        ]
        reply_markup = InlineKeyboardMarkup(keyboard)
        await update.message.reply_text("🔧 Pilih jenis trafik:", reply_markup=reply_markup)

# Pilih interface ➜ tampilkan pilihan periode
async def traffic_type_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    interface_key = query.data.replace("traffic_", "")

    # Simpan ke context
    context.user_data["interface"] = interface_key
    context.user_data['eth'] = INTERFACE_MAPPING[interface_key]
    # print(f'Interface key = {interface_key}\nInterface_eth = {INTERFACE_MAPPING[interface_key]}')
    keyboard = [
        [InlineKeyboardButton(period.title(), callback_data=f"period_{period}")]
        for period in PERIODS
    ]
    reply_markup = InlineKeyboardMarkup(keyboard)
    await query.edit_message_text(
        f"📊 Pilih periode grafik trafik untuk *{interface_key.upper()}*:",
        parse_mode="Markdown",
        reply_markup=reply_markup
    )

# Pilih periode ➜ ambil grafik dan statistik
async def traffic_period_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    period = query.data.replace("period_", "")
    interface_key = context.user_data.get("interface")
    interface_eth = context.user_data.get('eth')

    if not interface_key or interface_key not in INTERFACE_MAPPING:
        await query.edit_message_text("⚠️ Interface tidak valid.")
        return

    html = api_get_html(interface_eth)
    if not html:
        # print(f'Ini kode html get:\n{html}')
        await query.edit_message_text("⚠️ Gagal mengambil data dari server.")
        return

    result = extract_traffic_for_interface(html, interface_key, period)
    if not result:
        await query.edit_message_text("⚠️ Grafik tidak ditemukan.")
        return

    full_url = ROUTER_BASE_URL + "/graphs/iface/"+ interface_eth + "/" + result['image_url']

    # if full_url.startswith('/'):
    #     full_url = API_BASE_URL.replace("/api", "") + full_url
    # elif not full_url.startswith("http"):
    #     full_url = API_BASE_URL.replace("/api", "") + '/' + full_url

    caption = (
        f"*{period.title()} - {interface_key.upper()}*\n"
        f"📈 Max In: `{result['in_max']}` | Max Out: `{result['out_max']}`\n"
        f"📊 Avg In: `{result['in_avg']}` | Avg Out: `{result['out_avg']}`\n"
        f"📉 Current In: `{result['in_cur']}` | Current Out: `{result['out_cur']}`"
    )

    await query.message.reply_photo(
        photo=full_url,
        caption=caption,
        parse_mode="Markdown"
    )
