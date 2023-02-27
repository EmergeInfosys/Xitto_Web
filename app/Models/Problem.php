<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    use HasFactory;

    protected $fellable=[
        'title',
        'service_id',
        'description',
        'image',
        'price',
        'service_charge',
        'time',
        'quantity',
    ];
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
