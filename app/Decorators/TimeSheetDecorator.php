<?php

namespace App\Decorators;

use Carbon\Carbon;

class TimeSheetDecorator extends TimeSheetPreviewDecorator
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

        $startDate = Carbon::createFromFormat('Y-m-d', $this->object->start_date);
        $endDate = Carbon::createFromFormat('Y-m-d', $this->object->end_date);

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
                    $times[] = [
                        'id' => $projectTime->id,
                        'project_id' => $projectTime->project_id,
                        'projectName' => optional($projectTime->project)->name,
                        'hours' => $projectTime->hours,
                        'absence_type_id' => $projectTime->absence_type_id,
                        'absenceTypeName' => optional($projectTime->absence_type)->name,
                        'comment' => $projectTime->comment,
                        'date' => $date->locale('es')->translatedFormat('l, d F/Y'),
                        'customized' => $projectTime->customized,
                    ];
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
                        'comment' => ! is_null($item->comment) ? nl2br($item->comment) : null,
                    ];
                }),
            'reviewerComments' => $this
                ->timeSheetReviews()
                ->map(function ($item) {
                    return [
                        'date' => Carbon::parse(strval($item->updated_at))->locale('es')->translatedFormat('j/M/Y'),
                        'comment' => ! is_null($item->comment) ? nl2br($item->comment) : null,
                    ];
                }),
            'weeks' => $this->weeks(),
            'statusName' => $this->statusName(),
            'userName' => $this->userName(),
        ];
    }
}
