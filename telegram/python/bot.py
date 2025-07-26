from telegram.ext import ApplicationBuilder, CommandHandler
from config import BOT_TOKEN
# from handlers import start, register, status, traffic, download, help as help_module
from handlers import start, register, status, help as help_module

def main():
    app = ApplicationBuilder().token(BOT_TOKEN).build()

    app.add_handler(CommandHandler("start", start.start))
    app.add_handler(CommandHandler("register", register.register))
    app.add_handler(CommandHandler("status", status.status))
    # app.add_handler(CommandHandler("traffic", traffic.traffic))
    # app.add_handler(CommandHandler("download", download.download))
    app.add_handler(CommandHandler("help", help_module.help_command))

    print("🤖 Bot aktif...")
    app.run_polling()

if __name__ == "__main__":
    main()
