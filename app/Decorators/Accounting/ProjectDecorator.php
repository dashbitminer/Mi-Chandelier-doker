<?php

namespace App\Decorators\Accounting;

class ProjectDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function requireTimeSheetLabel()
    {
        return $this->object->require_time_sheet ? 'Si' : 'No';
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'name' => $this->object->name,
            'requireTimeSheet' => $this->object->require_time_sheet,
            'requireTimeSheetLabel' => $this->requireTimeSheetLabel(),
        ];
    }
}
