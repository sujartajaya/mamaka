from telegram.ext import ApplicationBuilder, CommandHandler, CallbackQueryHandler, MessageHandler, filters
from config import BOT_TOKEN
# from handlers import start, register, status, traffic, download, help as help_module
from handlers import start, register, status, help as help_module
from handlers.traffic import traffic, traffic_type_handler, traffic_period_handler
from handlers.download import download, download_handler, download_guest_date
from handlers import download

def main():
    app = ApplicationBuilder().token(BOT_TOKEN).build()

    app.add_handler(CommandHandler("start", start.start))
    app.add_handler(CommandHandler("register", register.register))
    app.add_handler(CommandHandler("status", status.status))
    app.add_handler(CommandHandler("help", help_module.help_command))
    app.add_handler(CommandHandler("traffic", traffic))
    app.add_handler(CallbackQueryHandler(traffic_type_handler, pattern=r"^traffic_"))
    app.add_handler(CallbackQueryHandler(traffic_period_handler, pattern=r"^period_"))
    app.add_handler(CommandHandler("download", download.download))
    app.add_handler(CallbackQueryHandler(download.download_handler, pattern=r"^download_"))
    app.add_handler(MessageHandler(filters.TEXT & filters.Regex(r"\d{4}-\d{2}-\d{2}"), download.download_guest_date))

    print("🤖 Bot aktif...")
    app.run_polling()

if __name__ == "__main__":
    main()
