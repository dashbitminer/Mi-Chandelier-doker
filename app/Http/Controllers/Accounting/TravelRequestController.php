<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Resources\Accounting\TravelRequestResource;
use App\Models\TravelRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PDF;

class TravelRequestController extends Controller
{
    public function index($countryCode)
    {
        $this->checkPermission('contabilidad.solicitudes-viaje');

        $user = auth()->user();
        $country = $this->country($countryCode);

        $travelRequests = TravelRequest::query()
            ->join('country_project_user', 'country_project_user.id', '=', 'travel_requests.country_project_user_id')
            ->join('country_projects', 'country_projects.id', '=', 'country_project_user.country_project_id')
            ->where('country_projects.country_id', $country->id)
            ->with('expenses')
            ->where('status', 'approved')
            ->select('travel_requests.*')
            ->orderByDesc('created_at')
            ->paginate(config('settings.per_page'));

        return Inertia::render('Chandelier/Accounting/TravelRequests/index', [
            'travelRequests' => TravelRequestResource::collection($travelRequests, 'list'),
        ]);
    }

    public function download(Request $request, $countryCode, string $id)
    {
        $this->checkPermission('contabilidad.solicitudes-viaje');

        $user = auth()->user();
        $country = $this->country($countryCode);

        $travelRequest = TravelRequest::query()
            ->join('country_project_user', 'country_project_user.id', '=', 'travel_requests.country_project_user_id')
            ->join('country_projects', 'country_projects.id', '=', 'country_project_user.country_project_id')
            ->where('country_projects.country_id', $country->id)
            ->with('expenses')
            ->where('status', 'approved')
            ->select('travel_requests.*')
            ->findOrFail($id);

        $resource = new TravelRequestResource($travelRequest, 'download');
        $data = $resource->resolve();

        $filename = 'solicitud-de-viaje-'.preg_replace('/[^A-Za-z0-9\-]/', '', $data['userName']).'.pdf';

        $pdf = PDF::loadView('pdf.Accounting.travel-request', [
            'travelRequest' => $data,
        ]);

        return $pdf->download($filename);
    }
}
