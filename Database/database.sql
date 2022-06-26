DROP TABLE IF EXISTS `sentry_projects_sentry_users`;
DROP TABLE IF EXISTS `sentry_codes`;
DROP TABLE IF EXISTS `sentry_users`;
DROP TABLE IF EXISTS `sentry_projects`;

CREATE TABLE sentry_users
(
    id      INTEGER PRIMARY KEY,
    email   TEXT,
    chat_id INTEGER
);

CREATE TABLE sentry_projects
(
    id   INTEGER PRIMARY KEY,
    slug TEXT
);

CREATE TABLE sentry_codes
(
    code       INTEGER,
    user_id    INTEGER,
    email      TEXT,
    created_at INTEGER
);

CREATE TABLE sentry_projects_sentry_users
(
    project_id INTEGER FOREIGN KEY REFERENCES sentry_projects(id),
    user_id INTEGER FOREIGN KEY REFERENCES sentry_users(id)
);