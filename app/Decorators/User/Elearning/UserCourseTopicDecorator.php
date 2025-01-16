<?php

namespace App\Decorators\User\Elearning;

class UserCourseTopicDecorator
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
            'status' => $this->object->status,
            'note' => $this->object->note,
            'evaluationStatus' => $this->object->evaluation_status,
        ];
    }
}
