<?php

namespace Merlinpanda\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        "email", "user_id", "priority", "email_verified_at"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
