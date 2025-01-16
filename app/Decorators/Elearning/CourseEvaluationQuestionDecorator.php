<?php

namespace App\Decorators\Elearning;

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
            'text' => $this->object->text,
            'priority' => $this->object->priority,
            'is_new' => 'false',
            'options' => $this
                ->options()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'text' => $item->text,
                        'priority' => $item->priority,
                        'is_new' => 'false',
                        'is_correct' => $item->is_correct ? 'true' : 'false',
                    ];
                }),
        ];
    }

    public function options()
    {
        return $this
            ->object
            ->options()
            ->orderBy('priority', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
