<?php

namespace App\Decorators;

use Carbon\Carbon;

class TimeSheetPreviewDecorator
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
            case 'incomplete':
                $statusName = 'Pendiente de completar';
                break;
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
                $statusName = 'Denegada';
                break;
            default:
                $statusName = '--';
                break;
        }

        return $statusName;
    }

    public function weekTitle($date)
    {
        return 'Semana del '.$date->locale('es')->translatedFormat('d F/Y').' al '.$date->copy()->endOfWeek()->locale('es')->translatedFormat('d F/Y');
    }

    public function timeSheetProjectTimes()
    {
        return $this
            ->object
            ->timeSheetProjectTimes()
            ->join('projects', 'time_sheet_project_times.project_id', '=', 'projects.id')
            ->select('time_sheet_project_times.*')
            ->orderBy('projects.name', 'desc')
            ->get();
    }

    public function timeSheetProjects()
    {
        return $this
            ->object
            ->timeSheetProjects()
            ->join('projects', 'time_sheet_projects.project_id', '=', 'projects.id')
            ->orderBy('projects.name', 'desc')
            ->select('time_sheet_projects.*')
            ->get();
    }

    public function timeSheetWeeks()
    {
        return $this
            ->object
            ->timeSheetWeeks()
            ->orderBy('week_of_year', 'asc')
            ->get();
    }

    public function timeSheetProjectWeeks()
    {
        return $this
            ->object
            ->timeSheetProjectWeeks()
            ->where('total_working_days', '>', '0')
            ->orderBy('time_sheet_project_id', 'asc')
            ->orderBy('week_of_year', 'asc')
            ->get();
    }

    public function timeSheetReviews()
    {
        return $this
            ->object->timeSheetReviews()
            ->where('queue', 'completed')
            ->where('status', 'rejected')
            ->orderByDesc('created_at')
            ->get();
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'status' => $this->object->status,
            'title' => $this->title(),
            'periodName' => $this->periodName(),
            'statusName' => $this->statusName(),
            'userName' => $this->userName(),
        ];
    }
}
