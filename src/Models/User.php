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

    public function appUser()
    {
        return $this->hasMany(AppUser::class);
    }

    public function apps()
    {
        return $this->hasManyThrough(App::class, AppUser::class);
    }

    public function firstNameWithPrefix()
    {
        return $this->first_name . "先生/女士";
    }

    public function mailUUIDs()
    {
        return $this->hasMany(UserMailUuid::class);
    }
}
