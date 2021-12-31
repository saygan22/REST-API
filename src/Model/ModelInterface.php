<?php declare(strict_types=1);

namespace Books\Model;

interface ModelInterface
{
    public static function getTableName(): string;

    public static function getSequenceName(): string;

    public static function fromArray(array $data): ?AbstractModel;

    public function toArray(): array;
}
