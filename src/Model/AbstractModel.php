<?php declare(strict_types=1);

namespace Books\Model;

use Books\Database\Db;

abstract class AbstractModel implements ModelInterface
{
    use IdentifiableTrait;

    public static function all(): ?array
    {
        $tableName = static::getTableName();

        $query = "SELECT * FROM $tableName";

        $statement = Db::get()->prepare($query);

        if (!$statement->execute()) {
            return null;
        }

        $data = $statement->fetchAll();

        return array_map(fn(array $row): AbstractModel => static::fromArray($row), $data);
    }

    public static function getBooks(): ?array
    {
        $tableName = static::getTableName();

        $query = "SELECT * FROM $tableName";

        $statement = Db::get()->prepare($query);

        $statement->execute();

        $data = $statement->fetchAll();

        return array_map(fn(array $row): AbstractModel => static::fromArray($row), $data);
    }

    public static function findBy(string $column, $value): ?AbstractModel
    {
        $tableName = static::getTableName();

        $query = "SELECT * FROM $tableName WHERE {$column} = :{$column}";

        $statement = Db::get()->prepare($query);

        if (!$statement->execute([$column => $value])) {
            return null;
        }

        $data = $statement->fetch();

        if ($data === false) {
            return null;
        }

        return static::fromArray($data);
    }

    public static function find(int $id): ?AbstractModel
    {
        return static::findBy('id', $id);
    }

    public function delete(): bool
    {
        $tableName = static::getTableName();

        $query = "DELETE FROM $tableName WHERE id = :id";

        $statement = Db::get()->prepare($query);

        if (!$statement->execute(['id' => $this->id])) {
            return false;
        }

        return true;
    }

    public function save(): int
    {
        $tableName = static::getTableName();

        $data = static::toArray();
        if ($this->id === null) unset($data['id']);

        $columns = implode(", ", array_keys($data));

        $placeholders = implode(", ", array_map(fn(string $column): string => ":$column", array_keys($data)));

        if ($this->id === null) {
            $query = "INSERT INTO $tableName ($columns) VALUES ($placeholders)";
        } else {
            $query = "UPDATE $tableName SET ($columns) = ($placeholders) WHERE id = :id";
        }

        $statement = Db::get()->prepare($query);

        $statement->execute(array_values($data));

        if ($this->id === null) {
            $this->id = (int)Db::get()->lastInsertId($this->getSequenceName());
        }

        return $this->id;
    }

    /** @inheritDoc */
    public static function getSequenceName(): string
    {
        return static::getTableName() . "_id_seq";
    }
}
