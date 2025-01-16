<?php

namespace App\Decorators;

class TimeSheetProjectDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function projectName()
    {
        return $this->object->project->name;
    }

    public function accumulatedHours()
    {
        return $this->object->timeSheetProjectTimes->sum('hours');
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'name' => $this->projectName(),
            'project_id' => $this->object->project_id,
            'budgetHours' => $this->object->hours,
            'hours' => 0,
            'accumulatedHours' => $this->accumulatedHours(),
        ];
    }
}
