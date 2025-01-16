<?php

namespace App\Decorators\User\Elearning;

class CourseEvaluationQuestionDecorator
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
            'priority' => $this->object->priority,
            'text' => $this->object->text,
            'options' => $this
                ->options()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'text' => $item->text,
                    ];
                }),
        ];
    }

    public function options()
    {
        return $this
            ->object
            ->options()
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
