<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelRequestExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_request_id',
        'expense_kind_id',
        'amount',
        'comment',
    ];

    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class);
    }

    public function expenseKind()
    {
        return $this->belongsTo(ExpenseKind::class);
    }
}
