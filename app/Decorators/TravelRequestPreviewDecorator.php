<?php

namespace App\Decorators;

use Carbon\Carbon;

class TravelRequestPreviewDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function arrivalDateFormatted()
    {
        return Carbon::parse(strval($this->object->arrival_date))->locale('es')->translatedFormat('j/M/Y');
    }

    public function departureDateFormatted()
    {
        return Carbon::parse(strval($this->object->departure_date))->locale('es')->translatedFormat('j/M/Y');
    }

    public function userName()
    {
        return $this->object->user->name;
    }

    public function reviewerName()
    {
        return $this->object->reviewer?->name;
    }

    public function projectName()
    {
        return $this->object->countryProjectUser->countryProject->project->name;
    }

    public function statusName()
    {
        $statusName = '';
        switch ($this->object->status) {
            case 'pending':
                $statusName = 'Requiere ajustes';
                break;
            case 'completed':
                $statusName = 'Pendiente de revision';
                break;
            case 'approved':
                $statusName = 'Aprobada';
                break;
            case 'rejected':
                $statusName = 'Rechazada';
                break;
            default:
                $statusName = '--';
                break;
        }

        return $statusName;
    }

    public function totalFormatted()
    {
        $total = 0;
        foreach ($this->object->expenses as $expense) {
            $total += $expense->amount;
        }

        return number_format($total, 2, '.', ',');
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'status' => $this->object->status,
            'statusName' => $this->statusName(),
            'departureDateFormatted' => $this->departureDateFormatted(),
            'arrivalDateFormatted' => $this->arrivalDateFormatted(),
            'totalFormatted' => $this->totalFormatted(),
            'userName' => $this->userName(),
            'reviewerName' => $this->reviewerName(),
            'projectName' => $this->projectName(),
        ];
    }
}
