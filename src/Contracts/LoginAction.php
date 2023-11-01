<?php

namespace Merlinpanda\Account\Contracts;

abstract class LoginAction
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
}
