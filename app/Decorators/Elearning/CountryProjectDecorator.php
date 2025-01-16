<?php

namespace App\Decorators\Elearning;

class CountryProjectDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function name()
    {
        return implode(' / ', [$this->object->project->name, $this->object->country->name]);
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'name' => $this->name(),
        ];
    }
}
