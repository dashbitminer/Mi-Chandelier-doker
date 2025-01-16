<?php

namespace App\Decorators\Accounting;

class PaymentPeriodDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function periodName()
    {
        $statusName = '';
        switch ($this->object->period) {
            case 'bimonthly':
                $statusName = 'Pago quincenal';
                break;
            case 'monthly':
                $statusName = 'Pago mensual';
                break;
            default:
                $statusName = '--';
                break;
        }

        return $statusName;
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'periodName' => $this->periodName(),
        ];
    }
}
