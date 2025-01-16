<?php

namespace App\Http\Resources\Backoffice;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->resource = $resource;
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
            'name' => $this->name,
        ];
    }
}
