<?php

namespace App\Http\Controllers\Accounting\Reports;

use App\Decorators\Accounting\TimeSheetProjectDecorator;
use App\Decorators\Accounting\TimeSheetTemplateEditDecorator;
use App\Decorators\ProjectDecorator;
use App\Http\Controllers\Controller;
use App\Http\Resources\Accounting\CountryProjectResource;
use App\Models\Project;
use App\Models\TimeSheetProject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PDF;

class TimeSheetStatusController extends Controller
{
    public function index($countryCode, string $id)
    {
        $user = auth()->user();

        $country = $this->country($countryCode);

        $timeSheetTemplate = $country->timeSheetTemplates()->where('status', 'publish')->findOrFail($id);

        $decorator = new TimeSheetTemplateEditDecorator($timeSheetTemplate);

        $projects = $country->countryProjects()
            ->join('projects', 'country_projects.project_id', '=', 'projects.id')
            ->orderBy('projects.name')
            ->orderBy('country_projects.id')
            ->select('country_projects.*')
            ->paginate(config('settings.per_page'));

        return Inertia::render('Chandelier/Accounting/Reports/TimeSheetStatuses/index', [
            'timeSheetTemplate' => $decorator->toArray(),
            'projects' => CountryProjectResource::collection($projects, 'list'),
        ]);
    }

    public function preview($countryCode, string $id, string $projectId)
    {
        $user = auth()->user();

        $country = $this->country($countryCode);

        $timeSheetTemplate = $country->timeSheetTemplates()->where('status', 'publish')->findOrFail($id);
        $timeSheetTemplateDecorator = new TimeSheetTemplateEditDecorator($timeSheetTemplate);

        $project = Project::findOrFail($projectId);
        $projectDecorator = new ProjectDecorator($project);

        $timeSheetProjectsIncompleted = $this->timeSheetProjectsIncompleted($timeSheetTemplate, $project, 8);
        $timeSheetProjectsIncompleted->getCollection()->transform(function ($item) {
            $decorator = new TimeSheetProjectDecorator($item);

            return $decorator->toArray();
        });

        $timeSheetProjectsCompleted = $this->timeSheetProjectsCompleted($timeSheetTemplate, $project, 8);
        $timeSheetProjectsCompleted->getCollection()->transform(function ($item) {
            $decorator = new TimeSheetProjectDecorator($item);

            return $decorator->toArray();
        });

        $timeSheetProjectsApproved = $this->timeSheetProjectsApproved($timeSheetTemplate, $project, 8);
        $timeSheetProjectsApproved->getCollection()->transform(function ($item) {
            $decorator = new TimeSheetProjectDecorator($item);

            return $decorator->toArray();
        });

        return Inertia::render('Chandelier/Accounting/Reports/TimeSheetStatuses/preview', [
            'project' => $projectDecorator->toArray(),
            'timeSheetTemplate' => $timeSheetTemplateDecorator->toArray(),
            'timeSheetProjectsIncompleted' => $timeSheetProjectsIncompleted,
            'timeSheetProjectsCompleted' => $timeSheetProjectsCompleted,
            'timeSheetProjectsApproved' => $timeSheetProjectsApproved,
        ]);
    }

    public function download(Request $request, $countryCode, string $id, string $projectId)
    {
        $user = auth()->user();

        $country = $this->country($countryCode);

        $timeSheetTemplate = $country->timeSheetTemplates()->where('status', 'publish')->findOrFail($id);
        $timeSheetTemplateDecorator = new TimeSheetTemplateEditDecorator($timeSheetTemplate);

        $project = Project::findOrFail($projectId);
        $projectDecorator = new ProjectDecorator($project);

        $statuses = explode(',', $request['data']['statuses']);
        $timeSheetProjectsIncompleted = [];
        if (in_array('incompleted', $statuses)) {
            $timeSheetProjectsIncompleted = $this->timeSheetProjectsIncompleted($timeSheetTemplate, $project);
            $timeSheetProjectsIncompleted->transform(function ($item) {
                $decorator = new TimeSheetProjectDecorator($item);

                return $decorator->toArray();
            });
        }

        $timeSheetProjectsCompleted = [];
        if (in_array('completed', $statuses)) {
            $timeSheetProjectsCompleted = $this->timeSheetProjectsCompleted($timeSheetTemplate, $project);
            $timeSheetProjectsCompleted->transform(function ($item) {
                $decorator = new TimeSheetProjectDecorator($item);

                return $decorator->toArray();
            });
        }

        $timeSheetProjectsApproved = [];
        if (in_array('approved', $statuses)) {
            $timeSheetProjectsApproved = $this->timeSheetProjectsApproved($timeSheetTemplate, $project);
            $timeSheetProjectsApproved->transform(function ($item) {
                $decorator = new TimeSheetProjectDecorator($item);

                return $decorator->toArray();
            });
        }

        $pdf = PDF::loadView('pdf.Accounting.Reports.time-sheet-statuses', [
            'project' => $projectDecorator->toArray(),
            'timeSheetTemplate' => $timeSheetTemplateDecorator->toArray(),
            'statuses' => $statuses,
            'timeSheetProjectsIncompleted' => $timeSheetProjectsIncompleted,
            'timeSheetProjectsCompleted' => $timeSheetProjectsCompleted,
            'timeSheetProjectsApproved' => $timeSheetProjectsApproved,
        ]);

        return $pdf->download($timeSheetTemplate->year.'-'.$timeSheetTemplate->month.'--informe-de-usuarios-para-hojas-de-tiempo.pdf');
    }

    private function timeSheetProjectsIncompleted($timeSheetTemplate, $project, $perPage = -1)
    {
        $query = $this->timeSheetProjectsQuery($timeSheetTemplate, $project)->whereIn('time_sheets.status', ['incomplete', 'pending']);

        return $perPage == -1 ? collect($query->get()) : $query->paginate($perPage);
    }

    private function timeSheetProjectsCompleted($timeSheetTemplate, $project, $perPage = -1)
    {
        $query = $this->timeSheetProjectsQuery($timeSheetTemplate, $project)->whereIn('time_sheets.status', ['completed']);

        return $perPage == -1 ? collect($query->get()) : $query->paginate($perPage);
    }

    private function timeSheetProjectsApproved($timeSheetTemplate, $project, $perPage = -1)
    {
        $query = $this->timeSheetProjectsQuery($timeSheetTemplate, $project)->whereIn('time_sheets.status', ['approved']);

        return $perPage == -1 ? collect($query->get()) : $query->paginate($perPage);
    }

    private function timeSheetProjectsQuery($timeSheetTemplate, $project)
    {
        return TimeSheetProject::join('time_sheets', 'time_sheet_projects.time_sheet_id', '=', 'time_sheets.id')
            ->join('time_sheet_templates', 'time_sheets.time_sheet_template_id', '=', 'time_sheet_templates.id')
            ->where('time_sheet_templates.id', $timeSheetTemplate->id)
            ->where('time_sheet_projects.project_id', $project->id)
            ->orderByDesc('time_sheets.created_at')
            ->select('time_sheet_projects.*');
    }
}
