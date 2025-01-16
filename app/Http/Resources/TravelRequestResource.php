<?php

namespace App\Http\Resources;

use App\Decorators\TravelRequestDecorator as Decorator;
use App\Decorators\TravelRequestExpenseDecorator as ExpenseDecorator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelRequestResource extends JsonResource
{
    private $view;

    private $decorated;

    public function __construct($resource, $view)
    {
        parent::__construct($resource);
        $this->resource = $resource;

        $this->view = $view;
        $this->decorated = new Decorator($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [];

        switch ($this->view) {
            case 'download':
                $data = $this->viewDownload();
                break;
            default:
                $data = $this->viewList();
        }

        return $data;
    }

    private function viewList()
    {
        return [
            'id' => $this->id,
            'travelDays' => $this->decorated->travelDays(),
            'arrivalDateFormatted' => $this->decorated->arrivalDateFormatted(),
            'departureDateFormatted' => $this->decorated->departureDateFormatted(),
            'projectName' => $this->decorated->projectName(),
            'userName' => $this->decorated->userName(),
            'totalFormatted' => $this->decorated->totalFormatted(),
        ];
    }

    private function viewDownload()
    {
        return [
            'id' => $this->id,
            'travelDays' => $this->decorated->travelDays(),
            'arrivalDateFormatted' => $this->decorated->arrivalDateFormatted(),
            'departureDateFormatted' => $this->decorated->departureDateFormatted(),
            'projectName' => $this->decorated->projectName(),
            'userName' => $this->decorated->userName(),
            'userPosition' => '',
            'reviewerName' => '',
            'reviewerPosition' => '',
            'totalFormatted' => $this->decorated->totalFormatted(),
            'description' => $this->description,
            'expenses' => $this->expenses->map(function ($item) {
                $decorated = new ExpenseDecorator($item);

                return [
                    'expenseKindName' => $decorated->expenseKindName(),
                    'amountFormatted' => $decorated->amountFormatted(),
                    'comment' => $item->comment,
                ];
            }),
        ];
    }
}
