<?php

namespace App\Decorators\Elearning;

class CourseDecorator extends CoursePreviewDecorator
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
            'status' => $this->object->status,
            'description' => $this->object->description,
            'scope' => $this->object->scope,
            'course_type' => $this->object->course_type,
            'category_id' => $this->object->category_id,
        ];
    }
}
