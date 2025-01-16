<?php

namespace App\Decorators;

class TravelRequestReviewPreviewDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function queueName()
    {
        $queueName = '';
        switch ($this->object->queue) {
            case 'pending':
                $queueName = 'Pendiente revision';
                break;
            case 'completed':
                $queueName = 'Revision completada';
                break;
            default:
                $queueName = '--';
                break;
        }

        return $queueName;
    }

    public function statusName()
    {
        $statusName = '';
        switch ($this->object->status) {
            case 'approved':
                $statusName = 'Aprobada';
                break;
            case 'denied':
                $statusName = 'No Aprobado (Requiere ajustes)';
                break;
            case 'rejected':
                $statusName = 'Rechazada';
                break;
            default:
                $statusName = '--';
                break;
        }

        return $statusName;
    }

    public function travelRequest()
    {
        $decorator = new TravelRequestPreviewDecorator($this->object->travelRequest);

        return $decorator->toArray();
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'status' => $this->object->status,
            'statusName' => $this->statusName(),
            'queue' => $this->object->queue,
            'queueName' => $this->queueName(),
            'travelRequest' => $this->travelRequest(),
        ];
    }
}
