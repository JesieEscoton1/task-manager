<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'language',
        'location',
        'status',
        'external_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}


