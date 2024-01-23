<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }

    function car() {
        return $this->belongsTo(Car::class, 'car_id');
    }
}
