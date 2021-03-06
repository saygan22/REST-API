<?php

namespace Books\Database;

class Db
{
    protected static $pdo = null;

    public static function get(): \PDO
    {
        return self::$pdo ?? (self::$pdo = new \PDO(
                'sqlite:hw-08.db',
                null,
                null,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                ]
            ));
    }
}
