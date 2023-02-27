<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class shoppingCart extends Model
{
    use HasFactory;
    protected $fillable = [
        'problem_id',
        'user_id'
    ];
     public function problem(){
        return $this->belongsTo(Problem::class);
     }
}
