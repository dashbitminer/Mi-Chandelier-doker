<?php

namespace App\Decorators\Accounting;

class AbsenceTypeDecorator
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
            'code' => $this->object->code,
        ];
    }
}
