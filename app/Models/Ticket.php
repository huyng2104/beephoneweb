<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_code',
        'customer_name',
        'customer_email',
        'customer_phone',
        'title',
        'description',
        'status',
        'admin_id'
    ];
}
