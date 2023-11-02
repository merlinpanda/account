<?php

namespace Merlinpanda\Account\Actions;

use Merlinpanda\Account\Models\User;

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

    public function handle(User $user, array $claims = []): string
    {
        $token = auth('api')->claims($claims)->login($user);

        return $this->formatToken($token);
    }
}
