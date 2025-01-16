<?php

namespace App\Operations;

use App\Models\Country;
use App\Models\PaymentPeriod;
use App\Models\TimeSheetTemplate;
use Carbon\Carbon;

class CreateTimeSheetTemplates
{
    const HOURS = 8;

    public $year;

    public $month;

    public $period;

    public $startDate;

    public $endDate;

    public $totalDays;

    public $initialDay;

    public $countries;

    public $timeSheetTemplates;

    public function __construct($year, $month, $period)
    {
        $this->year = $year;
        $this->month = $month;
        $this->period = $period;
    }

    public function perform(): void
    {
        $this->fetchCountries();
        if (! $this->isAbleToCreateTimeSheetTemplates()) {
            return;
        }
        $this->startDate = $this->startDate();
        $this->endDate = $this->endDate();
        $this->totalDays = $this->totalDays();
        $this->initialDay = $this->initialDay();
        $this->createTimeSheetTemplates();
    }

    public function createTimeSheetTemplates(): void
    {
        $this->timeSheetTemplates = [];

        foreach ($this->countries as $country) {

            $paymentPeriod = PaymentPeriod::where('country_id', $country->id)->first();
            if (! $paymentPeriod) {
                continue;
            }

            if (($this->period == 'first_period' || $this->period == 'second_period') && $paymentPeriod->period != 'bimonthly') {
                continue;
            }

            if ($this->period == 'month' && $paymentPeriod->period != 'monthly') {
                continue;
            }

            $timeSheetTemplate = $country->timeSheetTemplates()->where('year', $this->year)->where('month', $this->month)->where('period', $this->period)->first();
            if ($timeSheetTemplate) {
                continue;
            }

            $timeSheetTemplate = new TimeSheetTemplate;
            $timeSheetTemplate->month = $this->month;
            $timeSheetTemplate->year = $this->year;
            $timeSheetTemplate->status = 'unpublish';
            $timeSheetTemplate->period = $this->period;
            $timeSheetTemplate->start_date = $this->startDate->toDateString();
            $timeSheetTemplate->end_date = $this->endDate->toDateString();
            $timeSheetTemplate->registered_at = Carbon::now();
            if ($country->timeSheetTemplates()->save($timeSheetTemplate)) {
                $this->timeSheetTemplates[] = $timeSheetTemplate;
            }
        }
    }

    public function isAbleToCreateTimeSheetTemplates()
    {
        return count($this->countries) > 0;
    }

    public function fetchCountries(): void
    {
        $countries = Country::all();
        $this->countries = count($countries) > 0 ? $countries : [];
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
