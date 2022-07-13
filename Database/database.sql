DROP TABLE IF EXISTS `sentry_users`;

CREATE TABLE sentry_users
(
    telegram_id INTEGER PRIMARY KEY NOT NULL,
    email   TEXT NOT NULL UNIQUE,
    sentry_id INTEGER UNIQUE NOT NULL
);

DROP TABLE IF EXISTS `users_with_keys`;

CREATE TABLE users_with_keys
(
    telegram_id INTEGER PRIMARY KEY NOT NULL,
    project_id INTEGER NOT NULL,
    name   TEXT NOT NULL,
    CONSTRAINT AK_telegram_id_project_id UNIQUE(telegram_id, project_id)
);