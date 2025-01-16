<?php

namespace App\Decorators\Accounting;

use Carbon\Carbon;

class TimeSheetTemplatePreviewDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function monthName()
    {
        return Carbon::createFromFormat('!m', strval($this->object->month))->locale('es')->translatedFormat('F');
    }

    public function monthNameWithYear()
    {
        return implode(' ', [$this->monthName(), $this->object->year]);
    }

    public function title()
    {
        return implode(' / ', [$this->object->year, $this->monthName(), $this->periodName()]);
    }

    public function userName()
    {
        return $this->object->user->name;
    }

    public function periodName()
    {
        $statusName = '';
        switch ($this->object->period) {
            case 'first_period':
                $statusName = 'Primera quincena';
                break;
            case 'second_period':
                $statusName = 'Segunda quincena';
                break;
            case 'month':
                $statusName = 'Mensual';
                break;
            default:
                $statusName = '--';
                break;
        }

        return $statusName;
    }

    public function statusName()
    {
        $statusName = '';
        switch ($this->object->status) {
            case 'unpublish':
                $statusName = 'Sin publicar';
                break;
            case 'publish':
                $statusName = 'Publicada';
                break;
            default:
                $statusName = '--';
                break;
        }

        return $statusName;
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'status' => $this->object->status,
            'statusName' => $this->statusName(),
            'title' => $this->title(),
        ];
    }
}
