<?php

namespace App\Decorators;

class CountryProjectDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function requireWorkingWeekendLabel()
    {
        return $this->object->saturday_hours > 0 || $this->object->sunday_hours > 0 ? 'Si' : 'No';
    }

    public function requireTimeSheetLabel()
    {
        return $this->object->require_time_sheet ? 'Si' : 'No';
    }

    public function projectName()
    {
        return $this->object->project->name;
    }

    public function countryName()
    {
        return $this->object->country->name;
    }

    public function name()
    {
        return implode(' / ', [
            $this->projectName(),
            $this->countryName(),
        ]);
    }

    public function requireTimeSheet()
    {
        return $this->object->require_time_sheet ? 'true' : 'false';
    }
}
