<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailAutomation extends Model
{
    use HasFactory;

    protected $table = 'email_automation';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'message',
    ];
}
