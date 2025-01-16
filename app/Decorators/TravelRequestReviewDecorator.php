<?php

namespace App\Decorators;

class TravelRequestReviewDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function travelRequest()
    {
        $decorator = new TravelRequestDecorator($this->object->travelRequest);

        return $decorator->toArray();
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'status' => $this->object->status,
            'comment' => $this->object->comment,
            'travelRequest' => $this->travelRequest(),
        ];
    }
}
