<?php

namespace Merlinpanda\Account\Actions;

use Illuminate\Support\Facades\Event;
use Merlinpanda\Account\Events\OnFinishedLogin;
use Merlinpanda\Account\Models\User;
use Merlinpanda\Rbac\Models\App;

class BuildJwtToken
{
    const DEFAULT_AUTH_TYPE  = "Bearer";

    /**
     * @param string $token
     * @return string
     */
    protected function formatToken(string $token): string
    {
        return sprintf("%s %s", trim(self::DEFAULT_AUTH_TYPE), trim($token));
    }

    public function handle(User $user, App $app,string $method = "", array $claims = []): string
    {
        $token = auth('api')->claims($claims)->login($user);

        Event::dispatch(new OnFinishedLogin($user, $app, $method));

        return $this->formatToken($token);
    }
}
