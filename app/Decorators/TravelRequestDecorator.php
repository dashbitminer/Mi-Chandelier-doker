<?php

namespace App\Decorators;

use Carbon\Carbon;

class TravelRequestDecorator extends TravelRequestPreviewDecorator
{
    public function travelDays()
    {
        $startDate = Carbon::parse($this->object->departure_date);
        $endDate = Carbon::parse($this->object->arrival_date);

        return $startDate->diffInDays($endDate) + 1;
    }

    public function requestCashAdvanceLabel()
    {
        return $this->object->request_cash_advance ? 'Si' : 'No';
    }

    public function reviewerComments()
    {
        return $this->object->travelRequestReviews()->where('queue', 'completed')->whereIn('status', ['rejected', 'denied'])->orderByDesc('created_at')->get()->map(function ($item) {
            return [
                'date' => Carbon::parse(strval($item->updated_at))->locale('es')->translatedFormat('j/M/Y'),
                'comment' => $item->comment,
            ];
        });
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'status' => $this->object->status,
            'description' => $this->object->description,
            'arrival_date' => $this->object->arrival_date,
            'departure_date' => $this->object->departure_date,
            'request_cash_advance' => $this->object->request_cash_advance,
            'statusName' => $this->statusName(),
            'departureDateFormatted' => $this->departureDateFormatted(),
            'arrivalDateFormatted' => $this->arrivalDateFormatted(),
            'expenses' => $this->object->expenses->map(function ($item) {
                $decorator = new TravelRequestExpenseDecorator($item);

                return $decorator->toArray();
            }),
            'totalFormatted' => $this->totalFormatted(),
            'userName' => $this->userName(),
            'projectName' => $this->projectName(),
            'comment' => $this->object->comment,
            'travelDays' => $this->travelDays(),
            'requestCashAdvanceLabel' => $this->requestCashAdvanceLabel(),
            'reviewerComments' => $this->reviewerComments(),
        ];
    }
}
