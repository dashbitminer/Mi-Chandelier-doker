<?php

use App\Http\Controllers\Accounting\PaymentPeriodController as AccountingPaymentPeriodController;
use App\Http\Controllers\Accounting\ProjectController as AccountingProjectController;
use App\Http\Controllers\Accounting\Reports\TimeSheetController as AccountingReportTimeSheetController;
use App\Http\Controllers\Accounting\Reports\TimeSheetStatusController as AccountingReportTimeSheetStatusController;
use App\Http\Controllers\Accounting\Reports\TimeSheetUserController as AccountingReportTimeSheetUserController;
use App\Http\Controllers\Accounting\TimeSheetTemplateController as AccountingTimeSheetTemplateController;
use App\Http\Controllers\Accounting\TravelRequestController as AccountingTravelRequests;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Backoffice\UserController as BackofficeUserController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\Elearning\CategoryController as ElearningCategories;
use App\Http\Controllers\Elearning\CourseController as ElearningCourseController;
use App\Http\Controllers\Elearning\DeletedCourseController as ElearningDeletedCourseController;
use App\Http\Controllers\Elearning\TopicController as ElearningTopicController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TimeSheetController;
use App\Http\Controllers\TimeSheetReviewController;
use App\Http\Controllers\TravelRequestReviewController;
use App\Http\Controllers\TravelRequestsController;
use App\Http\Controllers\User\Elearning\CourseController as UserElearningCourses;
use App\Http\Controllers\User\Elearning\CourseEvaluationController as UserElearningCourseEvaluations;
use App\Http\Controllers\User\Elearning\HelpCenterController as UserElearningHelpCenter;
use App\Http\Controllers\User\Elearning\SubjectController as UserElearningSubject;
use App\Http\Controllers\User\Elearning\TopicController as UserElearningTopics;
use App\Http\Controllers\UserSupportController;
use App\Http\Middleware\HandleCountry;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

//Google Social Sign In
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

Route::get('/unassigned-user', [UserSupportController::class, 'unassigned'])->name('unassigned');
Route::get('/inactive-user', [UserSupportController::class, 'inactive'])->name('inactive');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // if (app()->environment('local')) {
    //     Route::get('/dashboard', function () {
    //         return Inertia::render('Dashboard');
    //     })->name('dashboard');
    // }

    Route::get('/', function () {
        return redirect('/SLV');
    });

    Route::get('/countries', [CountryController::class, 'index'])->name('countries');

    Route::prefix('{country}')->middleware(['web', HandleCountry::class])->group(function () {
        Route::inertia('/', 'Chandelier/Home/index');

        Route::resources([
            'projects' => ProjectController::class,
            'time-sheets' => TimeSheetController::class,
            'time-sheet-reviews' => TimeSheetReviewController::class,
            'travel-requests' => TravelRequestsController::class,
            'travel-request-reviews' => TravelRequestReviewController::class,
        ]);

        Route::prefix('travel-requests/{travel_request}/')->group(function () {
            Route::get('download', [TravelRequestsController::class, 'download'])->name('travel-requests.download');
        });

        Route::prefix('backoffice')->as('backoffice.')->group(function () {
            Route::resource('/users', BackofficeUserController::class)->only(['index', 'edit', 'update']);
            Route::prefix('users/{user}/')->group(function () {
                Route::put('toogle', [BackofficeUserController::class, 'toggle'])->name('users.toggle');
            });
        });

        Route::prefix('elearning')->as('elearning.')->group(function () {
            Route::resource('/courses', ElearningCourseController::class);
            Route::resource('/categories', ElearningCategories::class)->except(['show']);
            Route::prefix('categories/{category}/')->group(function () {
                Route::put('toggle', [ElearningCategories::class, 'toggle'])->name('categories.toggle');
            });

            Route::put('courses/{course}/toggle', [ElearningCourseController::class, 'toggle'])
                ->name('courses.toggle');

            Route::resource('deleted-courses', ElearningDeletedCourseController::class)->only([
                'index',
            ]);
            Route::post('deleted-courses/{course}/restore', [ElearningDeletedCourseController::class, 'restore'])
                ->name('deleted-courses.restore');

            Route::resource('courses.topics', ElearningTopicController::class);
        });

        Route::prefix('accounting')->as('accounting.')->group(function () {
            Route::resource('/time-sheet-templates', AccountingTimeSheetTemplateController::class);
            Route::resource('/projects', AccountingProjectController::class);
            Route::resource('/travel-requests', AccountingTravelRequests::class)->only(['index']);
            Route::prefix('travel-requests/{travel_request}/')->group(function () {
                Route::get('download', [AccountingTravelRequests::class, 'download'])->name('travel-requests.download');
            });
            Route::prefix('projects/{project}/')->group(function () {
                Route::put('toggle', [AccountingProjectController::class, 'toggle'])->name('projects.toggle');
            });

            Route::post('time-sheet-templates/{time_sheet_template}/publish', [AccountingTimeSheetTemplateController::class, 'publish'])
                ->name('time-sheet-templates.publish');

            Route::prefix('time-sheet-templates/{time_sheet_template}/')->as('time-sheet-templates.')->group(function () {
                Route::get('time-sheets', [AccountingReportTimeSheetController::class, 'index'])
                    ->name('time-sheets.index');
                Route::get('time-sheets/download', [AccountingReportTimeSheetController::class, 'download'])
                    ->name('time-sheets.download');

                Route::get('time-sheet-statuses', [AccountingReportTimeSheetStatusController::class, 'index'])
                    ->name('time-sheet-statuses.index');
                Route::get('time-sheet-statuses/{project}/preview', [AccountingReportTimeSheetStatusController::class, 'preview'])
                    ->name('time-sheet-statuses.preview');
                Route::get('time-sheet-statuses/{project}/download', [AccountingReportTimeSheetStatusController::class, 'download'])
                    ->name('time-sheet-statuses.download');

                Route::get('time-sheets-users', [AccountingReportTimeSheetUserController::class, 'index'])
                    ->name('time-sheets-users.index');
                Route::get('time-sheets-users/download', [AccountingReportTimeSheetUserController::class, 'download'])
                    ->name('time-sheets-users.download');
            });

            Route::get('payment-periods', [App\Http\Controllers\Accounting\PaymentPeriodController::class, 'index'])
                ->name('payment-periods');
            Route::get('payment-periods/create', [AccountingPaymentPeriodController::class, 'create'])
                ->name('payment-periods.create');
            Route::post('payment-periods/store', [AccountingPaymentPeriodController::class, 'store'])
                ->name('payment-periods.store');
        });

        Route::prefix('user')->as('user.')->group(function () {
            Route::prefix('elearning')->as('elearning.')->group(function () {
                Route::resource('/courses', UserElearningCourses::class)->only(['index']);

                Route::get('/courses/completed', [UserElearningCourses::class, 'completed'])->name('courses.completed');
                Route::get('help-center', [UserElearningHelpCenter::class, 'index'])->name('help-center.index');

                Route::prefix('courses/{course}/')->group(function () {
                    Route::resource('topics', UserElearningTopics::class)->only(['index', 'show']);
                    Route::get('certificate', [UserElearningTopics::class, 'certificate'])->name('courses.certificate');
                });

                Route::prefix('help-center/{help_center}/')->group(function () {
                    Route::resource('subjects', UserElearningSubject::class)->only(['index', 'show']);
                });

                Route::prefix('courses/{course}/topics/{topic}/')->group(function () {
                    Route::get('evaluation', [UserElearningCourseEvaluations::class, 'store'])
                        ->name('evaluations.store');
                    Route::post('evaluation', [UserElearningCourseEvaluations::class, 'create'])
                        ->name('evaluations.create');
                    Route::get('evaluation/results', [UserElearningCourseEvaluations::class, 'show'])
                        ->name('evaluations.show');
                });
            });
        });
    });
});
