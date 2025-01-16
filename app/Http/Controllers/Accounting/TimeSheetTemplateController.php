<?php

namespace App\Http\Controllers\Accounting;

use App\Decorators\Accounting\TimeSheetTemplateEditDecorator;
use App\Decorators\Accounting\TimeSheetTemplatePreviewDecorator;
use App\Http\Controllers\Controller;
use App\Models\TimeSheetTemplateHoliday;
use App\Operations\PublishTimeSheetTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TimeSheetTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($countryCode)
    {
        $this->checkPermission('contabilidad.hojas-tiempo');

        $user = auth()->user();

        $country = $this->country($countryCode);

        $timeSheetTemplates = $country->timeSheetTemplates()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(config('settings.per_page'));

        $timeSheetTemplates->getCollection()->transform(function ($item) {
            $decorator = new TimeSheetTemplatePreviewDecorator($item);

            return $decorator->toArray();
        });

        return Inertia::render('Chandelier/Accounting/TimeSheetTemplates/index', [
            'timeSheetTemplates' => $timeSheetTemplates,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($countryCode, string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($countryCode, string $id)
    {
        $this->checkPermission('contabilidad.hojas-tiempo');

        $user = auth()->user();

        $country = $this->country($countryCode);

        $timeSheetTemplate = $country->timeSheetTemplates()->where('status', 'unpublish')->findOrFail($id);

        $decorator = new TimeSheetTemplateEditDecorator($timeSheetTemplate);

        return Inertia::render('Chandelier/Accounting/TimeSheetTemplates/edit', [
            'timeSheetTemplate' => $decorator->toArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $countryCode, string $id)
    {
        $user = auth()->user();

        $country = $this->country($countryCode);

        $timeSheetTemplate = $country->timeSheetTemplates()->where('status', 'unpublish')->findOrFail($id);

        if (isset($request['data']['holidays'])) {
            foreach ($request['data']['holidays'] as $date) {
                $timeSheetTemplateHoliday = $timeSheetTemplate->timeSheetTemplateHolidays->where('date', $date)->first();
                if (! $timeSheetTemplateHoliday) {
                    $timeSheetTemplateHoliday = new TimeSheetTemplateHoliday;
                    $timeSheetTemplateHoliday->date = $date;

                    $timeSheetTemplate->timeSheetTemplateHolidays()->save($timeSheetTemplateHoliday);
                }
            }

            $timeSheetTemplate->timeSheetTemplateHolidays()->whereNotIn('date', $request['data']['holidays'])->delete();
        }

        return Redirect::route('accounting.time-sheet-templates.index', ['country' => $country->alpha_code]);
    }

    public function publish($countryCode, string $id)
    {
        $this->checkPermission('contabilidad.hojas-tiempo');

        $user = auth()->user();

        $country = $this->country($countryCode);

        $timeSheetTemplate = $country->timeSheetTemplates()->where('status', 'unpublish')->findOrFail($id);

        $timeSheetTemplateOperation = new PublishTimeSheetTemplate($timeSheetTemplate);
        $timeSheetTemplateOperation->perform();

        return Redirect::route('accounting.time-sheet-templates.index', ['country' => $country->alpha_code]);
    }
}
