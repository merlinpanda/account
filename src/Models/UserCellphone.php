<?php

namespace Merlinpanda\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCellphone extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id", "cellphone", "priority", "phone_verified_at"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
