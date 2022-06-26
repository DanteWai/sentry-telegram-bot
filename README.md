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
5. ```php initial_commands.php```

Переменные окружения(.env):
- APP_ENV - Переменная окружения (development|production|test)
- DATABASE_NAME - Название базы данных, например "test.db"
- TELEGRAM_SENTRY_BOT_TOKEN - Токен бота телеграм, который будет рассылать уведомления
- TELEGRAM_WEBHOOK_URL - url на который будут приходить сообщения из телеграм
- TELEGRAM_SUPER_ADMIN_CHAT_ID - Идентификатор админа
- TELEGRAM_TEST_CHAT_ID - Идентификатор тестового чата


Запуск тестов
```
vendor\bin\phpunit tests
```

Локальная разработка
php -S localhost:8000 Локальный веб сервер
ngrok http 8000 Доступ по внешнему адресу (для вебхука)
