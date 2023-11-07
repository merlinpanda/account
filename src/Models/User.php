<?php

namespace Merlinpanda\Account\Models;

use Merlinpanda\Rbac\Models\App;
use Merlinpanda\Rbac\Models\AppUser;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    const STATUS_NORMAL = "NORMAL";

    protected $hidden = ["password"];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emails()
    {
        return $this->hasMany(UserEmail::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cellphones()
    {
        return $this->hasMany(UserCellphone::class);
    }

    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function appUser()
    {
        return $this->hasMany(AppUser::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function apps()
    {
        return $this->hasManyThrough(App::class, AppUser::class);
    }

    /**
     * @return string
     */
    public function lastNameWithPrefix()
    {
        return $this->last_name . "先生/女士";
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mailUUIDs()
    {
        return $this->hasMany(UserMailUuid::class);
    }

    /**
     * @param string $email
     * @return null
     */
    public static function fetchUserByEmail(string $email)
    {
        $user_email = UserEmail::where([
            'email' => $email
        ])->whereNotNull('email_verified_at')->with('user')->first();

        if (!isset($user_email->id)) {
            return null;
        }

        return $user_email->user;
    }

    /**
     * @param string $cellphone
     * @param string $country_code
     * @return null
     */
    public static function fetchUserByPhone(string $cellphone, string $country_code)
    {
        $user_phone = UserCellphone::where([
            'cellphone' => phone($cellphone, $country_code)
        ])->whereNotNull('phone_verified_at')->with('user')->first();

        if (!isset($user_phone->id)) {
            return null;
        }

        return $user_phone->user;
    }
}
