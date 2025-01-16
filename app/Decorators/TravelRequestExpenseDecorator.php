<?php

namespace App\Decorators;

class TravelRequestExpenseDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function expenseKindName()
    {
        return $this->object->expenseKind->name;
    }

    public function amountFormatted()
    {
        return number_format($this->object->amount, 2, '.', ',');
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'expense_kind_id' => $this->object->expense_kind_id,
            'expenseKindName' => $this->expenseKindName(),
            'amount' => $this->object->amount,
            'amountFormatted' => $this->amountFormatted(),
            'comment' => $this->object->comment,
        ];
    }
}
