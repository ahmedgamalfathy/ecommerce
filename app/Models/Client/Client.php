<?php

namespace App\Models\Client;

use App\Models\Order\Order;
use App\Models\Client\ClientEmail;
use App\Models\Client\ClientPhone;
use App\Models\Client\ClientAdrress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Client extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function phones()
    {
        return $this->hasMany(ClientPhone::class);
    }

    public function addresses()
    {
        return $this->hasMany(ClientAdrress::class);
    }

    public function emails()
    {
        return $this->hasMany(ClientEmail::class);
    }
    public function ClientUser(){
         return $this->belongsTo(ClientUser::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
