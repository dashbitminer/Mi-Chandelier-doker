<?php

namespace App\Decorators\Elearning;

class TopicPreviewDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function requireEvaluationLabel()
    {
        return $this->object->require_evaluation ? 'Si' : 'No';
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'priority' => $this->object->priority,
            'name' => $this->object->name,
            'description' => $this->object->description,
            'requireEvaluationLabel' => $this->requireEvaluationLabel(),
        ];
    }
}
