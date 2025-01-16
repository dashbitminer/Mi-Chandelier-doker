<?php

namespace App\Operations;

use App\Models\User;

class PublishTimeSheetTemplate
{
    public $timeSheetTemplate;

    public $users;

    public $timeSheets;

    public function __construct($timeSheetTemplate)
    {
        $this->timeSheetTemplate = $timeSheetTemplate;
        $this->timeSheets = [];
    }

    public function perform(): void
    {
        $this->fetchUsers();
        if (! $this->isAbleToCreateTimeSheets()) {
            return;
        }
        $this->createTimeSheetSheets();
        $this->publish();
    }

    public function createTimeSheetSheets(): void
    {
        $this->timeSheets = [];

        foreach ($this->users as $user) {
            $timeSheetOperation = new \App\Operations\CreateTimeSheet($user, $this->timeSheetTemplate);
            $timeSheetOperation->perform();
            if ($timeSheetOperation->timeSheet) {
                $this->timeSheets[] = $timeSheetOperation->timeSheet;
            }
        }
    }

    public function publish(): void
    {
        if (count($this->timeSheets) == 0) {
            return;
        }

        $this->timeSheetTemplate->status = 'publish';
        $this->timeSheetTemplate->save();
    }

    public function isAbleToCreateTimeSheets(): bool
    {
        return count($this->users) > 0;
    }

    public function fetchUsers(): void
    {
        $users = User::all(); // TODO
        $this->users = count($users) > 0 ? $users : [];
    }
}
