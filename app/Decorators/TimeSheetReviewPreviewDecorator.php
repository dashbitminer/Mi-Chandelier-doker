<?php

namespace App\Decorators;

class TimeSheetReviewPreviewDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function timeSheet()
    {
        $decorator = new TimeSheetPreviewDecorator($this->object->timeSheet);

        return $decorator->toArray();
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'status' => $this->object->status,
            'queue' => $this->object->queue,
            'timeSheet' => $this->timeSheet(),
        ];
    }
}
