<?php

namespace App\Decorators\Elearning;

class CourseCountryDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function toArray()
    {
        return [
            'id' => $this->object->country_id,
            'name' => $this->object->country->name,
        ];
    }
}
