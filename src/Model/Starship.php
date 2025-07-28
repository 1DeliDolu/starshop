<?php

namespace App\Model;

class Starship
{
    private int $id;
    private string $name;
    private string $model;
    private string $manufacturer;
    private string $class;
    private string $captain;
    private string $status;

    public function __construct(int $id, string $name, string $model, string $manufacturer, string $class, string $captain, string $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->model = $model;
        $this->manufacturer = $manufacturer;
        $this->class = $class;
        $this->captain = $captain;
        $this->status = $status;
    }

    public function getId(): int
    {
        return $this->id;
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

    public function getClass(): string
    {
        return $this->class;
    }

    public function getCaptain(): string
    {
        return $this->captain;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
