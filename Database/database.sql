DROP TABLE IF EXISTS `sentry_users`;

CREATE TABLE sentry_users
(
    telegram_id INTEGER PRIMARY KEY NOT NULL,
    email   TEXT NOT NULL UNIQUE,
    sentry_id INTEGER UNIQUE NOT NULL
);