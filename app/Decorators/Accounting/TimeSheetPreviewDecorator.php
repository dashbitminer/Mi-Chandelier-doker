<?php

namespace App\Decorators\Accounting;

class TimeSheetPreviewDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function userName()
    {
        return $this->object->user->name;
    }

    public function reviewerName()
    {
        $leader = $this->object->user->leader;

        return $leader ? $leader->name : '';
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'userName' => $this->userName(),
            'reviewerName' => $this->reviewerName(),
        ];
    }
}
