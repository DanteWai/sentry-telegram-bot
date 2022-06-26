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
        $elements = [];
        $columns = implode(',',$columns);

        $sql = "select {$columns} from {$table}";

        if($condition){
            $sql .= " {$condition}";
        }

        $res = $this->client->query($sql);

        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $elements[] =  $row;
        }

        return $elements;
    }

    public function insert(string $table, array $attributes): int
    {
        $keys = array_keys($attributes);
        $columns = implode(',', $keys);
        $binding_values = implode(',', array_map(fn($item) => ':' . $item, $keys));

        $sql = "insert into {$table} ({$columns}) VALUES ({$binding_values})";

        $statement = $this->client->prepare($sql);

        foreach ($attributes as $name => $value) {
            $statement->bindValue(":{$name}", $value);
        }

        $statement->execute();
        return $this->client->lastInsertRowID();
    }

    public function update(string $table, array $attributes, string $condition = '')
    {
        $sql = "UPDATE {$table} SET";

        foreach ($attributes as $name => $value){
            $sql.= " {$name} = :{$name}";
        }

        if ($condition) {
            $sql .= " {$condition}";
        }

        $statement = $this->client->prepare($sql);

        foreach ($attributes as $name => $value) {
            $statement->bindValue(":{$name}", $value);
        }

        $statement->execute();
    }

    public function delete(string $table, string $condition = ''): void
    {
        $sql = "delete from {$table}";

        if ($condition) {
            $sql .= " {$condition}";
        }

        $this->client->query($sql);
    }
}