<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function tin_receipt()
    {
        return $this->belongsTo(PaymentReceiptTin::class, 'receipt_id', 'id');
    }

    public function columbite_receipt()
    {
        return $this->belongsTo(PaymentReceiptColumbite::class, 'receipt_id', 'id');
    }

    public function low_grade_columbite_receipt()
    {
        return $this->belongsTo(PaymentReceiptLowerGradeColumbite::class, 'receipt_id', 'id');
    }
}
