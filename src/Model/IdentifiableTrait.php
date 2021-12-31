<?php declare(strict_types=1);

namespace Books\Model;

trait IdentifiableTrait
{
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
