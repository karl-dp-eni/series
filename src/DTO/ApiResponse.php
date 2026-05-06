<?php

namespace App\DTO;

class ApiResponse
{

    /**
     * @var DragonDTO[]
     */
    private array $items = [];

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }


}
