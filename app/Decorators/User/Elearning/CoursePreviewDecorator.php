<?php

namespace App\Decorators\User\Elearning;

class CoursePreviewDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'name' => $this->object->name,
        ];
    }
}
