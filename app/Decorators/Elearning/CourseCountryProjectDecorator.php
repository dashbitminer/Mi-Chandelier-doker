<?php

namespace App\Decorators\Elearning;

class CourseCountryProjectDecorator
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
            'id' => $this->object->country_project_id,
            'name' => $this->name(),
        ];
    }
}
