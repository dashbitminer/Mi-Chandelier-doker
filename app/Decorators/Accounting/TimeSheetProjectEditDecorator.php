<?php

namespace App\Decorators\Accounting;

use Carbon\Carbon;

class TimeSheetProjectEditDecorator
{
    protected $object;

    public $timeSheet;

    public $weeks;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function weeks()
    {
        $projectTimes = $this->object->timeSheetProjectTimes()->get();

        $projectWeeks = $this->object->timeSheetProjectWeeks()->get();

        $timeSheetWeeks = $this->object->timeSheet->timeSheetWeeks()->get();

        $startDate = Carbon::createFromFormat('Y-m-d', $this->object->timeSheet->start_date);
        $endDate = Carbon::createFromFormat('Y-m-d', $this->object->timeSheet->end_date);

        $this->weeks = [];

        foreach ($timeSheetWeeks as $timeSheetWeek) {
            $week = [];
            $hours = 0;

            $firstDayOfWeek = Carbon::now()->setISODate($this->object->timeSheet->year, $timeSheetWeek->week_of_year);

            for ($day = 0; $day < 7; $day++) {
                $date = $firstDayOfWeek->copy()->addDay($day);

                if ($date->month != $this->object->timeSheet->month || ($date->day < $startDate->day || $date->day > $endDate->day)) {
                    $week[] = ['enable' => false];

                    continue;
                }

                $projectTime = $projectTimes->first(fn ($projectTime) => $projectTime->date == $date->toDateString());
                if (! $projectTime) {
                    continue;
                }

                $week[] = [
                    'enable' => true,
                    'dayName' => $date->locale('es')->translatedFormat('l'),
                    'day' => $date->day,
                    'hours' => $projectTime->hours,
                    'absenceTypeCode' => optional($projectTime->absence_type)->code,
                ];

                $hours += $projectTime->hours;
            }

            $projectWeek = $projectWeeks->first(fn ($projectWeek) => $projectWeek->week_of_year == $timeSheetWeek->week_of_year && $projectWeek->project_id == $this->object->project_id);

            $this->weeks[] = [
                'weekOfYear' => $timeSheetWeek->week_of_year,
                'totalWorkingDays' => $timeSheetWeek->total_working_days,
                'comment' => $projectWeek && ! is_null($projectWeek->comment) ? nl2br($projectWeek->comment) : null,
                'title' => $this->weekTitle($firstDayOfWeek),
                'days' => $week,
                'hours' => $hours,
            ];
        }

        return $this->weeks;
    }

    public function hours()
    {
        return intval($this->object->timeSheetProjectTimes()->sum('hours'));
    }

    public function timeSheetDecorated()
    {
        $decorator = new TimeSheetDecorator($this->object->timeSheet);

        return $decorator->toArray();
    }

    public function weekTitle($date)
    {
        return 'Semana del '.$date->locale('es')->translatedFormat('d F/Y').' al '.$date->copy()->endOfWeek()->locale('es')->translatedFormat('d F/Y');
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'projectName' => $this->object->project->name,
            'timeSheet' => $this->timeSheetDecorated(),
            'weeks' => $this->weeks(),
            'percentage' => round($this->object->percentage, 2),
            'hours' => $this->hours(),
        ];
    }
}
