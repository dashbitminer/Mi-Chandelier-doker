<?php

namespace App\Http\Controllers;

use App\Decorators\TimeSheetReviewDecorator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TimeSheetReviewController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show($country, string $id)
    {
        $this->checkPermission('operaciones.hojas-tiempo.review');

        $user = auth()->user();

        $timeSheetReview = $user->timeSheetReviews()->findOrFail($id);
        $decorator = new TimeSheetReviewDecorator($timeSheetReview);

        return Inertia::render('Chandelier/TimeSheetReviews/show', [
            'timeSheetReview' => $decorator->toArray(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($country, string $id)
    {
        $this->checkPermission('operaciones.hojas-tiempo.review');

        $user = auth()->user();

        $timeSheetReview = $user->timeSheetReviews()->where('queue', 'pending')->findOrFail($id);
        $decorator = new TimeSheetReviewDecorator($timeSheetReview);

        return Inertia::render('Chandelier/TimeSheetReviews/edit', [
            'timeSheetReview' => $decorator->toArray(),
            'timeSheetReviewStatuses' => [
                ['id' => 'approved', 'name' => 'Aprobado'],
                ['id' => 'rejected', 'name' => 'Rechazado'],
            ],
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
            'data.status' => 'required|string',
        ]);

        $timeSheetReview = $user->timeSheetReviews()->where('queue', 'pending')->findOrFail($id);
        $formData = $validated['data'];
        $timeSheetReview->comment = $formData['comment'];
        $timeSheetReview->status = $formData['status'];
        $timeSheetReview->queue = 'completed';
        $timeSheetReview->save();

        $timeSheet = $timeSheetReview->timeSheet;
        if ($timeSheetReview->status == 'approved') {
            $timeSheet->status = 'approved';
            $timeSheet->save();
        } else {
            $timeSheet->status = 'pending';
            $timeSheet->save();
        }

        return Redirect::route('time-sheets.index', ['country' => $country]);
    }
}
