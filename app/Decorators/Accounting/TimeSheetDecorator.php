<?php

namespace App\Decorators\Accounting;

use Carbon\Carbon;

class TimeSheetDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function title()
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $this->object->start_date);
        $endDate = Carbon::createFromFormat('Y-m-d', $this->object->end_date);

        return 'del '.$startDate->locale('es')->translatedFormat('d').' al '.$endDate->locale('es')->translatedFormat('d F Y');
    }

    public function userName()
    {
        return $this->object->user->name;
    }

    public function userPosition()
    {
        return '--';
    }

    public function reviewerName()
    {
        $reviewer = $this->object->reviewer;

        return $reviewer ? $reviewer->name : '';
    }

    public function reviewerPosition()
    {
        $reviewer = $this->object->reviewer;

        return '--';
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'title' => $this->title(),
            'userName' => $this->userName(),
            'userPosition' => $this->userPosition(),
            'reviewerName' => $this->reviewerName(),
            'reviewerPosition' => $this->reviewerPosition(),
            'comment' => $this->object->comment,
        ];
    }
}
