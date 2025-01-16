<?php

namespace App\Http\Resources\Accounting;

use App\Decorators\CountryProjectDecorator as Decorator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryProjectResource extends JsonResource
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
            case 'edit':
                $data = $this->viewEdit();
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
            'project_id' => $this->project_id,
            'name' => $this->decorated->name(),
            'requireWorkingWeekendLabel' => $this->decorated->requireWorkingWeekendLabel(),
            'requireTimeSheet' => $this->decorated->requireTimeSheet(),
            'requireTimeSheetLabel' => $this->decorated->requireTimeSheetLabel(),
        ];
    }

    private function viewEdit()
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'name' => $this->decorated->name(),
            'require_time_sheet' => $this->decorated->requireTimeSheet(),
            'saturday_hours' => $this->saturday_hours,
            'sunday_hours' => $this->sunday_hours,
        ];
    }
}
