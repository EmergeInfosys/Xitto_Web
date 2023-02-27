<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    
    public function brand()
    {
        return $this->hasMany(Brand::class);
    }
    public function problem()
    {
        return $this->hasMany(Problem::class);
    }

}
