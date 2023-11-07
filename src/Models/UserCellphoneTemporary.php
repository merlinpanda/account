<?php

namespace Merlinpanda\Account\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCellphoneTemporary extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id", "cellphone", "status", "verify_code", "expired_at"
    ];

    public function isExpired()
    {
        return Carbon::now()->gt($this->expired_at);
    }
}
