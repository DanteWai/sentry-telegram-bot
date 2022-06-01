<?php

namespace Database;

use SQLite3;

class DatabaseSQLLite extends Database
{
    public function __construct(string $db_name)
    {
        parent::__construct($db_name);
        $this->client = new SQLite3($_ENV['DATABASE_NAME']);
    }


    public function select(array $columns, string $table, string $condition = '')
    {
        // TODO: Implement select() method.
    }

    public function insert(string $table, array $attributes)
    {
        // TODO: Implement insert() method.
    }

    public function update(string $table, array $attributes, string $condition = '')
    {
        // TODO: Implement update() method.
    }

    public function delete(string $table, string $condition = '')
    {
        // TODO: Implement delete() method.
    }
}