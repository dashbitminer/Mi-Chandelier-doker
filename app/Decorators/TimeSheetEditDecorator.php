<?php

namespace App\Decorators;

use Carbon\Carbon;

class TimeSheetEditDecorator extends TimeSheetPreviewDecorator
{
    protected $object;

    public $weeks;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function weeks()
    {
        $projectTimes = $this->timeSheetProjectTimes();

        $timeSheetWeeks = $this->timeSheetWeeks();

        $timeSheetProjectWeeks = $this->timeSheetProjectWeeks();

        $startDate = Carbon::createFromFormat('Y-m-d', $this->object->start_date);
        $endDate = Carbon::createFromFormat('Y-m-d', $this->object->end_date);

        $startWeekNumberOfYear = $startDate->weekOfYear;
        $endWeekNumberOfYear = $endDate->weekOfYear;

        $this->weeks = [];

        foreach ($timeSheetWeeks as $timeSheetWeek) {
            $week = [];

            $firstDayOfWeek = Carbon::now()->setISODate($this->object->year, $timeSheetWeek->week_of_year);

            for ($day = 0; $day < 7; $day++) {
                $date = $firstDayOfWeek->copy()->addDay($day);

                if ($date->month != $this->object->month || ($date->day < $startDate->day || $date->day > $endDate->day)) {
                    $week[] = [];

                    continue;
                }

                $projectTimesByDate = $projectTimes->filter(fn ($projectTime) => $projectTime->date == $date->toDateString());

                $times = [];
                foreach ($projectTimesByDate as $projectTime) {
                    $times[] = $projectTime->id;
                }

                $week[] = [
                    'date' => $date->toDateString(),
                    'dayName' => $date->locale('es')->translatedFormat('l'),
                    'day' => $date->day,
                    'times' => $times,
                ];
            }

            $this->weeks[] = [
                'weekOfYear' => $timeSheetWeek->week_of_year,
                'title' => $this->weekTitle($firstDayOfWeek),
                'totalWorkingDays' => $timeSheetWeek->total_working_days,
                'days' => $week,
            ];
        }

        return $this->weeks;
    }

    public function times()
    {
        $projectTimes = $this->object->timeSheetProjectTimes;

        $this->times = [];

        foreach ($projectTimes as $projectTime) {
            $this->times[] = [
                'id' => $projectTime->id,
                'projectName' => $projectTime->project->name,
                'project_id' => $projectTime->project_id,
                'hours' => $projectTime->hours,
                'date' => $projectTime->date,
                'dateFormatted' => Carbon::parse(strval($projectTime->date))->locale('es')->translatedFormat('l j/M/Y'),
                'absence_type_id' => $projectTime->absence_type_id,
                'comment' => $projectTime->comment,
                'customized' => $projectTime->customized,
            ];
        }

        return $this->times;
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'status' => $this->object->status,
            'title' => $this->title(),
            'periodName' => $this->periodName(),
            'comment' => $this->object->comment,
            'user_acceptance' => $this->object->user_acceptance,
            'projects' => $this
                ->timeSheetProjects()
                ->map(function ($item) {
                    $decorator = new TimeSheetProjectDecorator($item);

                    return $decorator->toArray();
                }),
            'comments' => $this
                ->timeSheetProjectWeeks()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'project_id' => $item->project_id,
                        'weekOfYear' => $item->week_of_year,
                        'comment' => $item->comment,
                    ];
                }),
            'times' => $this->times(),
            'weeks' => $this->weeks(),
            'statusName' => $this->statusName(),
            'userName' => $this->userName(),
        ];
    }
}
