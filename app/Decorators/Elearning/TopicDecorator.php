<?php

namespace App\Decorators\Elearning;

class TopicDecorator extends TopicPreviewDecorator
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
            'priority' => $this->object->priority,
            'description' => $this->object->description,
            'content' => $this->object->content,
            'is_new' => 'false',
            'require_evaluation' => $this->object->require_evaluation ? 'true' : 'false',
        ];
    }
}
