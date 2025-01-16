<?php

namespace App\Http\Controllers\Accounting;

use App\Decorators\Accounting\PaymentPeriodDecorator;
use App\Http\Controllers\Controller;
use App\Models\PaymentPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PaymentPeriodController extends Controller
{
    public function index($countryCode)
    {
        $this->checkPermission('contabilidad.tipo-pago');

        $user = auth()->user();

        $country = $this->country($countryCode);

        $paymentPeriod = PaymentPeriod::where('country_id', $country->id)->first();

        $decorator = new PaymentPeriodDecorator($paymentPeriod);

        return Inertia::render('Chandelier/Accounting/PaymentPeriods/index', [
            'paymentPeriod' => $decorator->toArray(),
        ]);
    }

    public function create($countryCode)
    {
        $this->checkPermission('contabilidad.tipo-pago');

        $user = auth()->user();

        $country = $this->country($countryCode);

        $paymentPeriod = PaymentPeriod::where('country_id', $country->id)->first();

        return Inertia::render('Chandelier/Accounting/PaymentPeriods/create', [
            'periods' => collect([
                ['id' => 'bimonthly', 'name' => 'Pago quincenal'],
                ['id' => 'monthly', 'name' => 'Pago mensual'],
            ])->filter(function ($period) use ($paymentPeriod) {
                return $period['id'] != $paymentPeriod->period;
            }),
        ]);
    }

    public function store($countryCode, Request $request)
    {
        $user = auth()->user();

        $country = $this->country($countryCode);

        $paymentPeriod = PaymentPeriod::where('country_id', $country->id)->first();

        $validated = $request->validate([
            'data.comment' => 'nullable|string|max:255',
            'data.user_confirmation' => 'required|boolean',
            'data.period' => 'required|string',
        ]);

        $paymentPeriod->delete();

        $formData = $validated['data'];

        $newPaymentPeriod = new PaymentPeriod;
        $newPaymentPeriod->user_id = $user->id;
        $newPaymentPeriod->comment = $formData['comment'];
        $newPaymentPeriod->period = $formData['period'];
        $newPaymentPeriod->country_id = $country->id;
        $newPaymentPeriod->save();

        return Redirect::route('accounting.payment-periods', ['country' => $country->alpha_code]);
    }
}
