<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'title',
        'description',
        'media',
        'media_type',
        'on_facebook',
        'on_instagram',
        'on_linkedin',
        'scheduled_at',
        'draft',
        'posted',
    ];
}
