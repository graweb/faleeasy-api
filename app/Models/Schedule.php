<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'teacher',
        'day',
        'start_hour',
        'end_hour',
        'subject',
        'speak',
        'listen',
        'read',
        'write',
        'situation',
    ];

    protected $dates = ['expire_at', 'created_at', 'updated_at', 'deleted_at'];

    protected $hidden = [
        'expire_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
