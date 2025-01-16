<?php

namespace App\Decorators;

class TimeSheetReviewDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function timeSheet()
    {
        $decorator = new TimeSheetDecorator($this->object->timeSheet);

        return $decorator->toArray();
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'status' => $this->object->status,
            'comment' => $this->object->comment,
            'timeSheet' => $this->timeSheet(),
        ];
    }
}
