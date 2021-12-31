<?php

namespace Books\Model;

use Books\Database\Db;

class User extends AbstractModel
{
    private string $username;

    private string $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public static function createTable(): void
    {
        $db = Db::get();
        $db->query('CREATE TABLE IF NOT EXISTS `users` (
            `id` INTEGER PRIMARY KEY AUTOINCREMENT,
            `username` TEXT NOT NULL,
            `password` TEXT NOT NULL
        )');
    }

    public function getPassword(): string
    {
        return $this->password;
    }


    /** @inheritDoc */
    public static final function getTableName(): string
    {
        return "users";
    }

    /** @inheritDoc */
    public static final function fromArray(array $data): ?AbstractModel
    {
        if (!isset($data['id']) || !isset($data['username']) || !isset($data['password'])) {
            return null;
        }

        $user = new User($data['username'], $data['password']);
        $user->id = (int)$data['id'];

        return $user;
    }

    /** @inheritDoc */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password
        ];
    }

}