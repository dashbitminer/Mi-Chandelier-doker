<?php

namespace App\Http\Controllers\Accounting\Reports;

use App\Decorators\Accounting\AbsenceTypeDecorator;
use App\Decorators\Accounting\TimeSheetProjectEditDecorator;
use App\Decorators\Accounting\TimeSheetTemplateEditDecorator;
use App\Http\Controllers\Controller;
use App\Http\Resources\Accounting\TimeSheetResource;
use App\Models\AbsenceType;
use App\Models\Project;
use App\Models\TimeSheetProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PDF;

class TimeSheetUserController extends Controller
{
    public function index($countryCode, string $id)
    {
        $user = auth()->user();

        $country = $this->country($countryCode);

        $timeSheetTemplate = $country->timeSheetTemplates()->where('status', 'publish')->findOrFail($id);
        $decorator = new TimeSheetTemplateEditDecorator($timeSheetTemplate);

        $timeSheets = $timeSheetTemplate->timeSheets()
            ->join('users', 'time_sheets.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('time_sheets.*')
            ->paginate(config('settings.per_page'));

        return Inertia::render('Chandelier/Accounting/Reports/TimeSheetsUsers/index', [
            'timeSheetTemplate' => $decorator->toArray(),
            'timeSheets' => TimeSheetResource::collection($timeSheets, 'list'),
        ]);
    }

    public function download(Request $request, $countryCode, string $id)
    {
        $user = auth()->user();

        $country = $this->country($countryCode);

        $timeSheetTemplate = $country->timeSheetTemplates()->where('status', 'publish')->findOrFail($id);
        $timeSheetTemplateDecorator = new TimeSheetTemplateEditDecorator($timeSheetTemplate);

        $userIDs = explode(',', $request['data']['users']);

        $timeSheetProjects = $this->timeSheetProjects($timeSheetTemplate, $userIDs);
        $timeSheetProjects->transform(function ($item) {
            $decorator = new TimeSheetProjectEditDecorator($item);

            return $decorator->toArray();
        });

        $absenceTypes = collect(AbsenceType::orderBy('name', 'asc')->get());

        $absenceTypes->transform(function ($item) {
            $decorator = new AbsenceTypeDecorator($item);

            return $decorator->toArray();
        });

        $pdf = PDF::loadView('pdf.Accounting.Reports.time-sheets-users', [
            'timeSheetTemplate' => $timeSheetTemplateDecorator->toArray(),
            'timeSheetProjects' => $timeSheetProjects,
            'absenceTypes' => $absenceTypes,
        ]);

        if (count($userIDs) == 1) {
            $user = Project::find(current($userIDs));
            $filename = 'hojas-de-tiempo-'.preg_replace('/[^A-Za-z0-9\-]/', '', $user->name).'.pdf';
        } else {
            $monthName = Carbon::createFromFormat('!m', strval($timeSheetTemplate->month))->locale('es')->translatedFormat('F');
            $filename = 'hojas-de-tiempo-'.$monthName.'.pdf';
        }

        return $pdf->download($filename);
    }

    private function timeSheetProjects($timeSheetTemplate, $userIDs)
    {
        return collect(TimeSheetProject::join('time_sheets', 'time_sheet_projects.time_sheet_id', '=', 'time_sheets.id')
            ->join('projects', 'projects.id', '=', 'time_sheet_projects.project_id')
            ->join('time_sheet_templates', 'time_sheets.time_sheet_template_id', '=', 'time_sheet_templates.id')
            ->where('time_sheet_templates.id', $timeSheetTemplate->id)
            ->whereIn('time_sheets.user_id', $userIDs)
            ->orderBy('projects.name')
            ->orderByDesc('time_sheets.created_at')
            ->select('time_sheet_projects.*')
            ->get());
    }
}
