<?php

namespace App\Models\Client;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientEmail extends Model
{
    use CreatedUpdatedBy,HasFactory;
    protected $table = 'client_emails';
    protected $guarded = [];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
