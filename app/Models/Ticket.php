<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_code',
        'customer_name',
        'customer_email',
        'title',
        'description',
        'priority',
        'status',
        'admin_id'
    ];
}
