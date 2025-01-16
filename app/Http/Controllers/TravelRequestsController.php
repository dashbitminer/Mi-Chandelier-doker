<?php

namespace App\Http\Controllers;

use App\Decorators\TravelRequestDecorator;
use App\Decorators\TravelRequestPreviewDecorator;
use App\Decorators\TravelRequestReviewPreviewDecorator;
use App\Http\Resources\TravelRequestResource;
use App\Models\CountryProjectUser;
use App\Models\ExpenseKind;
use App\Models\TravelRequest;
use App\Models\TravelRequestExpense;
use App\Models\TravelRequestReview;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use PDF;

class TravelRequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $countryCode)
    {
        $this->checkPermission('operaciones.solicitudes-viaje');

        $user = auth()->user();
        $tenDaysAgo = Carbon::now()->subDays(10);

        $travelRequestsActive = $user->travelRequests()->with('expenses')
            ->where('created_at', '>=', $tenDaysAgo)
            ->orderByDesc('created_at')
            ->paginate(2, ['*'], 'active_page');

        $travelRequestsActive->getCollection()->transform(function ($item) {
            $decorator = new TravelRequestPreviewDecorator($item);

            return $decorator->toArray();
        });

        $travelRequestsExpired = $user->travelRequests()->with('expenses')
            ->where('created_at', '<', $tenDaysAgo)
            ->orderByDesc('created_at')
            ->paginate(2, ['*'], 'expired_page');

        $travelRequestsExpired->getCollection()->transform(function ($item) {
            $decorator = new TravelRequestPreviewDecorator($item);

            return $decorator->toArray();
        });

        $travelRequestReviews = $user->travelRequestReviews()
            ->where('queue', 'pending')
            ->orderByDesc('created_at')
            ->paginate(2, ['*'], 'reviews_page');

        $travelRequestReviews->getCollection()->transform(function ($item) {
            $decorator = new TravelRequestReviewPreviewDecorator($item);

            return $decorator->toArray();
        });

        return Inertia::render('Chandelier/TravelRequests/index', [
            'travelRequestsActive' => $travelRequestsActive,
            'travelRequestsExpired' => $travelRequestsExpired,
            'travelRequestReviews' => $travelRequestReviews,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($countryCode): Response
    {
        $user = auth()->user();

        $this->checkPermission('operaciones.solicitudes-viaje');

        $country = $this->country($countryCode);

        $projects = $user->countryProjectUsers()
            ->join('country_projects', 'country_projects.id', '=', 'country_project_user.country_project_id')
            ->where('country_projects.country_id', $country->id)
            ->with('project')
            ->get()
            ->pluck('project');

        $expense_kinds = ExpenseKind::all();

        return Inertia::render('Chandelier/TravelRequests/create', [
            'projects' => $projects,
            'expense_kinds' => $expense_kinds,
        ]);
    }

    /**
     * Consideraciones cuando se crea una solicitud de viaje:
     * - se asocia al usuario que la creo
     * - su estado es 'completed', bloqueando su edicion. Solo puede volverse a desbloquearse si su estado se cambia a
     *   'pending', lo cual ocurre cuando la persona encargada de aprobar lo deniega
     * - se crea un solicitud para su revision, asignadose esta revision a la persona encargada de ello
     */
    public function store($countryCode, Request $request): RedirectResponse
    {
        $user = auth()->user();

        $country = $this->country($countryCode);

        $countryProjectUser = $user->countryProjectUsers()
            ->join('country_projects', 'country_projects.id', '=', 'country_project_user.country_project_id')
            ->where('country_projects.country_id', $country->id)
            ->where('country_projects.project_id', $request->data['project_id'])
            ->select('country_project_user.*')
            ->first();

        if ($countryProjectUser) {
            $request->merge([
                'data' => array_merge($request->get('data', []), [
                    'country_project_user_id' => $countryProjectUser->id,
                ]),
            ]);
        }

        $validated = $request->validate([
            'data' => 'required|array',
            'data.country_project_user_id' => 'required|exists:country_project_user,id',
            'data.description' => 'required|string',
            'data.departure_date' => 'required|date',
            'data.arrival_date' => 'required|date|after_or_equal:departure_date',
            'data.request_cash_advance' => 'boolean',
            'data.expenses' => 'required|array',
            'data.expenses.*.expense_kind_id' => 'required|exists:expense_kinds,id',
            'data.expenses.*.amount' => 'required|numeric|min:0',
            'data.expenses.*.comment' => 'required|string',
        ]);
        $formData = $validated['data'];
        $travelRequest = new TravelRequest($formData);
        $travelRequest->user_id = $user->id;
        $travelRequest->status = 'completed';
        if ($travelRequest->save()) {
            foreach ($formData['expenses'] as $expense) {
                $travelRequest->expenses()->create($expense);
            }
        }

        $projectUserToReview = CountryProjectUser::query()
            ->join('country_projects', 'country_projects.id', '=', 'country_project_user.country_project_id')
            ->where('country_projects.project_id', $countryProjectUser->project_id)
            ->where('country_projects.country_id', $country->id)
            ->whereNotIn('user_id', [$countryProjectUser->user_id])
            ->where('is_leader', true)
            ->select('country_project_user.*')
            ->first();

        if ($projectUserToReview) {
            $reviewerID = $projectUserToReview->user_id;

            $travelRequest->reviewer_id = $reviewerID;
            $travelRequest->save();

            $travelRequestReview = new TravelRequestReview;
            $travelRequestReview->user_id = $reviewerID;
            $travelRequestReview->queue = 'pending';
            $travelRequestReview->registered_at = Carbon::now();
            $travelRequest->travelRequestReviews()->save($travelRequestReview);
        }

        return Redirect::route('travel-requests.index', ['country' => $countryCode]);
    }

    /**
     * Display the specified resource.
     */
    public function show($countryCode, $id): Response
    {
        $this->checkPermission('operaciones.solicitudes-viaje');

        $user = auth()->user();

        $travelRequest = $user->travelRequests()->with(['countryProjectUser', 'expenses', 'expenses.expenseKind'])
            ->findOrFail($id);

        $decorator = new TravelRequestDecorator($travelRequest);

        return Inertia::render('Chandelier/TravelRequests/show', [
            'travelRequest' => $decorator->toArray(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($countryCode, $id): Response
    {
        $this->checkPermission('operaciones.solicitudes-viaje');

        $user = auth()->user();

        $country = $this->country($countryCode);

        $travelRequest = $user->travelRequests()->whereIn('status', ['pending'])
            ->with(['expenses', 'expenses.expenseKind', 'countryProjectUser', 'countryProjectUser.project'])
            ->findOrFail($id);

        $decorator = new TravelRequestDecorator($travelRequest);

        $expenseKinds = ExpenseKind::all();

        $currentProject = $travelRequest->countryProjectUser->project;

        $projects = $user->countryProjectUsers()
            ->join('country_projects', 'country_projects.id', '=', 'country_project_user.country_project_id')
            ->where('country_projects.country_id', $country->id)
            ->with('project')
            ->get()
            ->pluck('project');

        return Inertia::render('Chandelier/TravelRequests/edit', [
            'travelRequest' => $decorator->toArray(),
            'expenseKinds' => $expenseKinds,
            'projects' => $projects,
            'currentProject' => $currentProject,
        ]);
    }

    public function update(Request $request, $countryCode, $id)
    {
        try {
            $user = auth()->user();

            $country = $this->country($countryCode);

            $request->merge($request->input('data'));

            $travelRequest = $user->travelRequests()->where('status', ['pending'])->findOrFail($id);

            $countryProjectUser = $user->countryProjectUsers()
                ->join('country_projects', 'country_projects.id', '=', 'country_project_user.country_project_id')
                ->where('country_projects.country_id', $country->id)
                ->where('country_projects.project_id', $request->data['project_id'])
                ->select('country_project_user.*')
                ->first();

            $request->merge(['country_project_user_id' => $countryProjectUser->id, 'status' => 'completed']);

            $travelRequest->update($request->except('expenses'));

            if ($request->has('expensesToDelete')) {
                $travelRequest->expenses()->whereIn('id', $request->input('expensesToDelete'))->delete();
            }

            if ($request->has('expenses')) {
                foreach ($request->input('expenses') as $expense) {
                    TravelRequestExpense::updateOrCreate(
                        ['id' => $expense['id'] ?? null],
                        [
                            'travel_request_id' => $travelRequest->id,
                            'expense_kind_id' => $expense['expense_kind_id'],
                            'amount' => $expense['amount'],
                            'comment' => $expense['comment'],
                        ]
                    );
                }
            }

            $previousTravelRequestReview = $travelRequest->travelRequestReviews()->first();
            $reviewerID = null;
            if ($previousTravelRequestReview) {
                $reviewerID = $previousTravelRequestReview->user_id;
            } else {
                $projectUserToReview = CountryProjectUser::query()
                    ->join('country_projects', 'country_projects.id', '=', 'country_project_user.country_project_id')
                    ->where('country_projects.project_id', $countryProjectUser->project_id)
                    ->where('country_projects.country_id', $country->id)
                    ->whereNotIn('user_id', [$countryProjectUser->user_id])
                    ->where('is_leader', true)
                    ->select('country_project_user.*')
                    ->first();

                if ($projectUserToReview) {
                    $reviewerID = $projectUserToReview->user_id;
                }
            }

            if ($reviewerID) {
                $travelRequest->reviewer_id = $reviewerID;
                $travelRequest->save();

                $travelRequestReview = new TravelRequestReview;
                $travelRequestReview->user_id = $reviewerID;
                $travelRequestReview->queue = 'pending';
                $travelRequestReview->registered_at = Carbon::now();
                $travelRequest->travelRequestReviews()->save($travelRequestReview);
            }

            return Redirect::route('travel-requests.index', ['country' => $countryCode]);
        } catch (\Exception $e) {

        }
    }

    public function download(Request $request, $countryCode, string $id)
    {
        $this->checkPermission('operaciones.solicitudes-viaje');

        $country = $this->country($countryCode);

        $user = auth()->user();

        $travelRequest = $user->travelRequests()->where('status', 'approved')->findOrFail($id);
        $resource = new TravelRequestResource($travelRequest, 'download');
        $data = $resource->resolve();

        $filename = 'solicitud-de-viaje-'.preg_replace('/[^A-Za-z0-9\-]/', '', $data['userName']).'.pdf';

        $pdf = PDF::loadView('pdf.travel-request', [
            'travelRequest' => $data,
        ]);

        return $pdf->download($filename);
    }
}
