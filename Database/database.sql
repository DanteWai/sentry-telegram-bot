DROP TABLE IF EXISTS `sentry_users`;

CREATE TABLE sentry_users
(
    telegram_id INTEGER PRIMARY KEY NOT NULL,
    email   TEXT NOT NULL UNIQUE,
    sentry_id INTEGER UNIQUE NOT NULL
);

DROP TABLE IF EXISTS `project_user_with_keys`;

CREATE TABLE project_user_with_keys
(
    project_id INTEGER NOT NULL,
    user_with_key_id INTEGER NOT NULL,

    FOREIGN KEY (user_with_key_id) REFERENCES users_with_key(telegram_id) ON DELETE CASCADE
);

DROP TABLE IF EXISTS `users_with_key`;

CREATE TABLE users_with_key
(
    telegram_id INTEGER PRIMARY KEY NOT NULL,
    name   TEXT NOT NULL,
    CONSTRAINT AK_telegram_id_project_id UNIQUE(telegram_id)
);