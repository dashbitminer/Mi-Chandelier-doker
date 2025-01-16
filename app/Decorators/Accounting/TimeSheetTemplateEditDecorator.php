<?php

namespace App\Decorators\Accounting;

use Carbon\Carbon;

class TimeSheetTemplateEditDecorator extends TimeSheetTemplatePreviewDecorator
{
    protected $object;

    public $weeks;

    public $holidays;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function holidays()
    {
        return $this
            ->object
            ->timeSheetTemplateHolidays()
            ->orderBy('date')
            ->get();
    }

    public function weeks()
    {
        $holidays = $this
            ->object
            ->timeSheetTemplateHolidays()
            ->orderBy('date')
            ->get();

        $startDate = Carbon::createFromFormat('Y-m-d', $this->object->start_date);
        $endDate = Carbon::createFromFormat('Y-m-d', $this->object->end_date);

        $startWeekNumberOfYear = $startDate->weekOfYear;
        $endWeekNumberOfYear = $endDate->weekOfYear;

        $this->weeks = [];

        for ($weekNumberOfYear = $startWeekNumberOfYear; $weekNumberOfYear <= $endWeekNumberOfYear; $weekNumberOfYear++) {
            $week = [];

            $firstDayOfWeek = Carbon::now()->setISODate($this->object->year, $weekNumberOfYear);

            for ($day = 0; $day < 7; $day++) {
                $date = $firstDayOfWeek->copy()->addDay($day);

                if ($date->month != $this->object->month || ($date->day < $startDate->day || $date->day > $endDate->day)) {
                    $week[] = [];

                    continue;
                }

                $isHoliday = count($holidays->filter(fn ($holiday) => $holiday->date == $date->toDateString())) > 0;

                $week[] = [
                    'date' => $date->toDateString(),
                    'dayName' => $date->locale('es')->translatedFormat('l'),
                    'day' => $date->day,
                    'isWeekend' => ($date->isSunday() || $date->isSaturday()),
                ];
            }

            $this->weeks[] = $week;
        }

        return $this->weeks;
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'status' => $this->object->status,
            'statusName' => $this->statusName(),
            'title' => $this->title(),
            'monthNameWithYear' => $this->monthNameWithYear(),
            'weeks' => $this->weeks(),
            'holidays' => $this
                ->holidays()
                ->map(function ($item) {
                    return $item->date;
                }),
            'dayNames' => [
                'Lunes',
                'Martes',
                'Miercoles',
                'Jueves',
                'Viernes',
                'Sabado',
                'Domingo',
            ],
        ];
    }
}
