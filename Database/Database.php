<?php

namespace Database;

abstract class Database
{
    protected string $db_name;
    protected string $db_user;
    protected string $db_pass;
    protected $client;

    public function __construct(string $db_name, string $db_user = '', string$db_pass = '')
    {
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
    }

    abstract public function select(array $columns, string $table, string $condition = '');
    abstract public function insert(string $table, array $attributes);
    abstract public function update(string $table, array $attributes, string $condition = '');
    abstract public function delete(string $table, string $condition = '');
}