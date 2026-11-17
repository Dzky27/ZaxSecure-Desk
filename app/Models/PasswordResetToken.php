<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'password_reset_tokens';

    /**
     * Indicates if the model should have a timestamp.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    /**
     * Get the created at timestamp.
     */
    public function getCreatedAtAttribute($value)
    {
        return $value;
    }
}
