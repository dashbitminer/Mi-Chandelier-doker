<?php

namespace App\Http\Resources\Accounting;

use App\Decorators\Accounting\TimeSheetDecorator as Decorator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeSheetResource extends JsonResource
{
    private $decorated;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->resource = $resource;

        $this->decorated = new Decorator($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->viewList();
    }

    private function viewList()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'userName' => $this->decorated->userName(),
        ];
    }
}
