<?php

namespace App\Http\Resources\Backoffice;

use App\Decorators\UserDecorator as Decorator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'firstName' => $this->decorated->firstName(),
            'lastName' => $this->decorated->lastName(),
            'name' => $this->decorated->name(),
            'active' => $this->active ? 'true' : 'false',
            'role' => $this->decorated->role(),
        ];
    }
}
