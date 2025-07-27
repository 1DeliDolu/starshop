<?php

namespace App\Model;

class Starship
{
    private int $id;
    private string $name;
    private string $model;
    private string $manufacturer;

    public function __construct(int $id, string $name, string $model, string $manufacturer)
    {
        $this->id = $id;
        $this->name = $name;
        $this->model = $model;
        $this->manufacturer = $manufacturer;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }
}
