# Оповещение об ошибках в sentry через telegram

Стек:
- PHP 7.4|8.*
- SqlLite3

Развертка:
1. ```composer install```
2. Создать SqlLite базу в корне проекта ```sqlite3 telegramDB.db```
3. Создать и настроить файл .env
4. Импортировать таблицы 
```
sqlite3 ./telegramDB.db
.read ./Database/database.sql
.exit
```

Переменные окружения(.env):
- TELEGRAM_SENTRY_BOT_TOKEN - Токен бота телеграм, который будет рассылать уведомления
- DATABASE_NAME - Название базы данных, например "test.db"

