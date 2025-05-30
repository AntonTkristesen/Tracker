<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'time',
        'description',
    ];

    /**
     * Get the user that owns the tracking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}