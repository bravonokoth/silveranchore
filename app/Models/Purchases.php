<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'quantity', 'cost', 'supplier', 'purchase_date'];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}