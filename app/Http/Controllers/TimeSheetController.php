<?php

namespace App\Http\Controllers;

use App\Decorators\TimeSheetDecorator;
use App\Decorators\TimeSheetEditDecorator;
use App\Decorators\TimeSheetPreviewDecorator;
use App\Decorators\TimeSheetReviewPreviewDecorator;
use App\Models\AbsenceType;
use App\Models\TimeSheetReview;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TimeSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkPermission('operaciones.hojas-tiempo');

        $user = auth()->user();

        $timeSheets = $user->timeSheets()
            ->orderByDesc('created_at')
            ->paginate(config('settings.per_page'));

        $timeSheets->getCollection()->transform(function ($item) {
            $decorator = new TimeSheetPreviewDecorator($item);

            return $decorator->toArray();
        });

        $timeSheetReviews = $user->timeSheetReviews()->where('queue', 'pending')
            ->orderByDesc('created_at')
            ->paginate(config('settings.per_page'));

        $timeSheetReviews->getCollection()->transform(function ($item) {
            $decorator = new TimeSheetReviewPreviewDecorator($item);

            return $decorator->toArray();
        });

        return Inertia::render('Chandelier/TimeSheets/index', [
            'timeSheets' => $timeSheets,
            'timeSheetReviews' => $timeSheetReviews,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($country, string $id)
    {
        $this->checkPermission('operaciones.hojas-tiempo');

        $user = auth()->user();

        $timeSheet = $user->timeSheets()->findOrFail($id);
        $decorator = new TimeSheetDecorator($timeSheet);

        return Inertia::render('Chandelier/TimeSheets/show', [
            'timeSheet' => $decorator->toArray(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($country, string $id)
    {
        $this->checkPermission('operaciones.hojas-tiempo');

        $user = auth()->user();

        $timeSheet = $user->timeSheets()->whereIn('status', ['incomplete', 'pending'])->findOrFail($id);
        $decorator = new TimeSheetEditDecorator($timeSheet);

        $absenceTypes = AbsenceType::all();

        return Inertia::render('Chandelier/TimeSheets/edit', [
            'timeSheet' => $decorator->toArray(),
            'absenceTypes' => $absenceTypes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $country, string $id)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'data.comment' => 'nullable|string|max:255',
            'data.user_acceptance' => 'required|boolean',
            'data.times.*.id' => 'required|integer',
            'data.times.*.hours' => 'required|numeric',
            'data.times.*.absence_type_id' => 'nullable|integer|exists:absence_types,id',
            'data.times.*.comment' => 'nullable|string',
            'data.comments.*.id' => 'required|integer',
            'data.comments.*.comment' => 'required|string',
        ]);

        $timeSheet = $user->timeSheets()->whereIn('status', ['incomplete', 'pending'])->findOrFail($id);
        $formData = $validated['data'];
        $timeSheet->status = 'completed';

        if ($timeSheet->save()) {
            if (isset($formData['times'])) {
                foreach ($formData['times'] as $timeSheetProjectTimeParams) {
                    $timeSheetProjectTime = $timeSheet->timeSheetProjectTimes->find($timeSheetProjectTimeParams['id']);
                    if ($timeSheetProjectTime) {
                        $timeSheetProjectTime->hours = $timeSheetProjectTimeParams['hours'];
                        $timeSheetProjectTime->absence_type_id = $timeSheetProjectTimeParams['absence_type_id'];
                        $timeSheetProjectTime->comment = $timeSheetProjectTimeParams['comment'];
                        $timeSheetProjectTime->customized = $this->projectTimeCustomized($timeSheetProjectTime);
                        $timeSheetProjectTime->save();
                    }
                }
            }

            if (isset($formData['comments'])) {
                foreach ($formData['comments'] as $timeSheetProjectWeekParams) {
                    $timeSheetProjectWeek = $timeSheet->timeSheetProjectWeeks->find($timeSheetProjectWeekParams['id']);
                    if ($timeSheetProjectWeek) {
                        $timeSheetProjectWeek->comment = $timeSheetProjectWeekParams['comment'];
                        $timeSheetProjectWeek->save();
                    }
                }
            }
        }

        $reviewerID = $user->leader_id;

        if ($reviewerID) {
            $timeSheetReview = new TimeSheetReview;
            $timeSheetReview->user_id = $reviewerID;
            $timeSheetReview->queue = 'pending';
            $timeSheetReview->registered_at = Carbon::now();
            $timeSheet->timeSheetReviews()->save($timeSheetReview);
        }

        $decorator = new TimeSheetEditDecorator($timeSheet);

        return response()->json($decorator->toArray());
    }

    private function projectTimeCustomized($projectTime)
    {
        if (floatval($projectTime->hours) != floatval($projectTime->original_hours)) {
            return true;
        }
        if (! is_null($projectTime->comment)) {
            return true;
        }
        if (! empty($projectTime->comment)) {
            return true;
        }
        if (! is_null($projectTime->absence_type_id)) {
            return true;
        }

        return false;
    }
}
