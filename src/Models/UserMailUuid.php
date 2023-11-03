<?php

namespace Merlinpanda\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMailUuid extends Model
{
    use HasFactory;

    protected $fillable = [
        "html", "email", "subject", "user_id", "uuid"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
