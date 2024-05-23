<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'email';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'password_reset_tokens';

    protected $fillable = ['email', 'token', 'created_at'];
}
