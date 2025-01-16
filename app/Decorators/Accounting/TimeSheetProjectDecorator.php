<?php

namespace App\Decorators\Accounting;

class TimeSheetProjectDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function userName()
    {
        return $this->object->timeSheet->user->name;
    }

    public function userEmail()
    {
        return $this->object->timeSheet->user->email;
    }

    public function reviewerName()
    {
        $leader = $this->object->timeSheet->user->leader;

        return $leader ? $leader->name : '';
    }

    public function reviewerEmail()
    {
        $leader = $this->object->timeSheet->user->leader;

        return $leader ? $leader->email : '';
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'userName' => $this->userName(),
            'userEmail' => $this->userEmail(),
            'reviewerName' => $this->reviewerName(),
            'reviewerEmail' => $this->reviewerEmail(),
        ];
    }
}
