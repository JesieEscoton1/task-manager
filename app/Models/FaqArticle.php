<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'content',
        'locale',
        'category',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];
}


