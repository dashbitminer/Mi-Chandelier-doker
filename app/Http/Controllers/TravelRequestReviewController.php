<?php

namespace App\Http\Controllers;

use App\Decorators\TravelRequestReviewDecorator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TravelRequestReviewController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show($country, string $id)
    {

        $this->checkPermission('operaciones.solicitudes-viaje.review');

        $user = auth()->user();

        $travelRequestReview = $user->travelRequestReviews()->findOrFail($id);
        $decorator = new TravelRequestReviewDecorator($travelRequestReview);

        return Inertia::render('Chandelier/TravelRequestReviews/show', [
            'travelRequestReview' => $decorator->toArray(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($country, string $id)
    {
        $this->checkPermission('operaciones.solicitudes-viaje.review');

        $user = auth()->user();
        $travelRequestReview = $user->travelRequestReviews()->where('queue', 'pending')->findOrFail($id);
        $decorator = new TravelRequestReviewDecorator($travelRequestReview);

        return Inertia::render('Chandelier/TravelRequestReviews/edit', [
            'travelRequestReview' => $decorator->toArray(),
            'travelRequestReviewStatuses' => [
                ['id' => 'approved', 'name' => 'Aprobado'],
                ['id' => 'denied', 'name' => 'No Aprobado (Requiere ajustes)'],
                ['id' => 'rejected', 'name' => 'Rechazada'],
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

        $travelRequestReview = $user->travelRequestReviews()->where('queue', 'pending')->findOrFail($id);
        $formData = $validated['data'];
        if (array_key_exists('comment', $formData)) {
            $travelRequestReview->comment = $formData['comment'];
        }
        $travelRequestReview->status = $formData['status'];
        $travelRequestReview->queue = 'completed';
        $travelRequestReview->save();

        $travelRequest = $travelRequestReview->travelRequest;

        switch ($travelRequestReview->status) {
            case 'approved':
                $newStatus = 'approved';
                break;
            case 'rejected':
                $newStatus = 'rejected';
                break;
            default:
                $newStatus = 'pending';
        }

        $travelRequest->status = $newStatus;
        $travelRequest->save();

        return Redirect::route('travel-requests.index', ['country' => $country]);
    }
}
