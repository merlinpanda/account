<?php

namespace Merlinpanda\Account\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Merlinpanda\Account\Contracts\AbnormalUser;

class User extends Authenticatable implements JWTSubject, AbnormalUser
{
    use HasFactory, Notifiable;

    protected $hidden = ["password"];

    public function emails()
    {
        return $this->hasMany(UserEmail::class);
    }

    public function cellphones()
    {
        return $this->hasMany(UserCellphone::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * 没有绑定账号
     *
     * @return bool
     */
    public function doesNotHaveAccount(): bool
    {
        return false;
    }

    /**
     * 是否长时间未登录
     *
     * @return bool
     */
    public function isLongTimeNotLogin(): bool
    {
        return false;
    }

    /**
     * 新客户端登录
     *
     * @return bool
     */
    public function isNewClientLogin(): bool
    {
        return false;
    }

    /**
     * 不是常在地区
     *
     * @return bool
     */
    public function isEmergencyArea(): bool
    {
        return false;
    }
}
