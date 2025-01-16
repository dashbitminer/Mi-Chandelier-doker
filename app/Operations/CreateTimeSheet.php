<?php

namespace App\Operations;

use App\Models\TimeSheet;
use App\Models\TimeSheetProject;
use App\Models\TimeSheetProjectTime;
use App\Models\TimeSheetProjectWeek;
use App\Models\TimeSheetWeek;
use Carbon\Carbon;

class CreateTimeSheet
{
    const HOURS = 8;

    public $user;

    public $template;

    public $reviewer;

    public $year;

    public $month;

    public $period;

    public $startDate;

    public $endDate;

    public $totalDays;

    public $initialDay;

    public $countryProjectUsers;

    public $holidays;

    public $timeSheet;

    public $timeSheetProjects;

    public $timeSheetWeeks;

    public $timeSheetProjectTimes;

    public function __construct($user, $template)
    {
        $this->user = $user;
        $this->template = $template;

        $this->reviewer = $user->leader;
        $this->year = $template->year;
        $this->month = $template->month;
        $this->period = $template->period;
    }

    public function perform(): void
    {
        if (! $this->isValid()) {
            return;
        }
        $this->fetchProjects();
        $this->fetchHolidays();
        if (! $this->isAbleToCreateTimeSheet()) {
            return;
        }
        $this->startDate = $this->startDate();
        $this->endDate = $this->endDate();
        $this->totalDays = $this->totalDays();
        $this->initialDay = $this->initialDay();
        $this->createTimeSheet();
        $this->createTimeSheetProjects();
        $this->createTimeSheetWeeks();
        $this->createTimeSheetProjectTimes();
    }

    public function createTimeSheet(): void
    {
        $timeSheet = new TimeSheet;
        $timeSheet->month = $this->month;
        $timeSheet->year = $this->year;
        $timeSheet->status = 'incomplete';
        $timeSheet->period = $this->period;
        $timeSheet->start_date = $this->startDate->toDateString();
        $timeSheet->end_date = $this->endDate->toDateString();
        $timeSheet->registered_at = Carbon::now();
        $timeSheet->time_sheet_template_id = $this->template->id;
        $timeSheet->reviewer_id = $this->reviewer->id;
        if ($this->user->timeSheets()->save($timeSheet)) {
            $this->timeSheet = $timeSheet;
        }
    }

    public function createTimeSheetProjects(): void
    {
        $this->timeSheetProjects = [];

        $hours = $this->period == 'month' ? 160 : 80;
        $minutes = $hours * 60;
        $leftMinutes = $minutes;
        $index = 0;
        $totalProjects = count($this->countryProjectUsers);

        foreach ($this->countryProjectUsers as $countryProjectUser) {
            $index++;

            $timeSheetProject = $this->timeSheet->timeSheetProjects()->where('project_id', $countryProjectUser->project_id)->first();
            if ($timeSheetProject) {
                continue;
            }

            $countryProject = $countryProjectUser->countryProject;
            if (! $countryProject) {
                continue;
            }

            $timeSheetProject = new TimeSheetProject;

            $timeSheetProject->country_project_id = $countryProject->id;
            $timeSheetProject->project_id = $countryProject->project_id;

            $timeSheetProjectMinutes = round($minutes * $countryProjectUser->salary_distribution);
            $timeSheetProjectHours = round($timeSheetProjectMinutes / 60);
            if ($index === $totalProjects) {
                $timeSheetProjectHours = round($leftMinutes / 60);
            }

            $timeSheetProject->hours = $timeSheetProjectHours;
            $timeSheetProject->percentage = round(self::HOURS * $countryProjectUser->salary_distribution, 2);
            if ($this->timeSheet->timeSheetProjects()->save($timeSheetProject)) {
                $this->timeSheetProjects[] = $timeSheetProject;
            }

            $leftMinutes -= $timeSheetProjectMinutes;
        }
    }

    public function createTimeSheetWeeks(): void
    {
        $this->timeSheetWeeks = [];

        $startWeekNumberOfYear = $this->startDate->weekOfYear;
        $endWeekNumberOfYear = $this->endDate->weekOfYear;

        for ($i = $startWeekNumberOfYear; $i <= $endWeekNumberOfYear; $i++) {
            $timeSheetWeek = $this->timeSheet->timeSheetWeeks()->where('week_of_year', $i)->first();
            if ($timeSheetWeek) {
                continue;
            }

            $timeSheetWeek = new TimeSheetWeek;

            $timeSheetWeek->week_of_year = $i;

            if ($this->timeSheet->timeSheetWeeks()->save($timeSheetWeek)) {
                $this->timeSheetWeeks[] = $timeSheetWeek;

                foreach ($this->timeSheetProjects as $timeSheetProject) {
                    $timeSheetProjectWeek = new TimeSheetProjectWeek;

                    $timeSheetProjectWeek->time_sheet_project_id = $timeSheetProject->id;
                    $timeSheetProjectWeek->time_sheet_week_id = $timeSheetWeek->id;
                    $timeSheetProjectWeek->week_of_year = $timeSheetWeek->week_of_year;
                    $timeSheetProjectWeek->project_id = $timeSheetProject->project_id;
                    $timeSheetProjectWeek->country_project_id = $timeSheetProject->country_project_id;

                    $totalWorkingDays = 0;
                    $firstDayOfWeek = Carbon::now()->setISODate($this->template->year, $timeSheetWeek->week_of_year);
                    for ($day = 0; $day < 7; $day++) {
                        $date = $firstDayOfWeek->copy()->addDay($day);
                        if ($this->isAbleDate($date, $timeSheetProject->countryProject)) {
                            $totalWorkingDays += 1;
                        }
                    }
                    $timeSheetProjectWeek->total_working_days = $totalWorkingDays;

                    $this->timeSheet->timeSheetProjectWeeks()->save($timeSheetProjectWeek);
                }

                $timeSheetWeek->total_working_days = $timeSheetWeek->timeSheetProjectWeeks->where('week_of_year', $timeSheetWeek->week_of_year)->max('total_working_days');
                $timeSheetWeek->save();
            }
        }
    }

    public function createTimeSheetProjectTimes(): void
    {
        $this->timeSheetProjectTimes = [];

        for ($i = 0; $i < $this->totalDays; $i++) {
            $day = $this->initialDay + $i;
            foreach ($this->timeSheetProjects as $timeSheetProject) {
                $date = Carbon::createFromDate($this->timeSheet->year, $this->timeSheet->month, $day);

                if (! $this->isAbleDate($date, $timeSheetProject->countryProject)) {
                    continue;
                }

                $timeSheetProjectTime = $timeSheetProject->timeSheetProjectTimes()->where('date', $date->toDateString())->first();
                if ($timeSheetProjectTime) {
                    continue;
                }

                $timeSheetProjectTime = new TimeSheetProjectTime;

                $timeSheetProjectTime->time_sheet_id = $this->timeSheet->id;
                $timeSheetProjectTime->project_id = $timeSheetProject->project_id;
                $timeSheetProjectTime->date = $date->toDateString();
                $timeSheetProjectTime->hours = 0;
                $timeSheetProjectTime->original_hours = 0;
                $timeSheetProjectTime->customized = false;
                if ($timeSheetProject->timeSheetProjectTimes()->save($timeSheetProjectTime)) {
                    $this->timeSheetProjectTimes[] = $timeSheetProjectTime;
                }
            }
        }
    }

    public function isValid(): bool
    {
        if (is_null($this->year)) {
            return false;
        }

        if (is_null(! $this->month)) {
            return false;
        }

        if (is_null(! $this->period)) {
            return false;
        }

        return ! is_null($this->reviewer);
    }

    public function isAbleToCreateTimeSheet(): bool
    {
        if (count($this->countryProjectUsers) == 0) {
            return false;
        }

        $timeSheets = $this->user->timeSheets
            ->where('year', $this->year)
            ->where('month', $this->month)
            ->where('period', $this->period);

        return count($timeSheets) == 0;
    }

    public function isAbleDate($date, $countryProject)
    {
        if ($this->isDateInHolidays($date)) {
            return false;
        }

        if (! $this->isDateInMonth($date)) {
            return false;
        }

        if ($date->isSunday()) {
            return $this->isAbleDateInSunday($countryProject);
        }

        if ($date->isSaturday()) {
            return $this->isAbleDateInSaturday($countryProject);
        }

        return true;
    }

    public function isDateInHolidays($date)
    {
        return in_array($date->toDateString(), $this->holidays);
    }

    public function isDateInMonth($date)
    {
        if ($date->month != $this->template->month) {
            return false;
        }

        return $date->day >= $this->startDate->day && $date->day <= $this->endDate->day ? true : false;
    }

    public function isAbleDateInSunday($countryProject)
    {
        return $countryProject && $countryProject->sunday_hours > 0;
    }

    public function isAbleDateInSaturday($countryProject)
    {
        return $countryProject && $countryProject->saturday_hours > 0;
    }

    public function fetchProjects(): void
    {
        $countryProjects = $this->user->countryProjects->where('require_time_sheet', true);

        $countryProjectUsers = $this->user->countryProjectUsers->whereIn('country_project_id', $countryProjects->pluck('id'));
        $this->countryProjectUsers = count($countryProjectUsers) > 0 ? $countryProjectUsers : [];
    }

    public function fetchHolidays(): void
    {
        $holidays = $this->template->timeSheetTemplateHolidays->map(function ($holiday) {
            return $holiday->date;
        });
        $this->holidays = count($holidays) > 0 ? $holidays->toArray() : [];
    }

    public function startDate()
    {
        $day = $this->period == 'second_period' ? 16 : 1;

        return Carbon::createFromDate($this->year, $this->month, $day);
    }

    public function endDate()
    {
        if ($this->period == 'first_period') {
            return Carbon::createFromDate($this->year, $this->month, 15);
        }

        $beginningOfMonth = Carbon::createFromDate($this->year, $this->month, 1);

        return $beginningOfMonth->endOfMonth();
    }

    public function totalDays()
    {
        return intval($this->startDate->diffInDays($this->endDate)) + 1;
    }

    public function initialDay()
    {
        return $this->startDate->day;
    }
}
