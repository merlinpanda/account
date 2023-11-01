<?php

namespace Merlinpanda\Account\Exceptions;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AccountOrPasswordNotMatchException extends UnauthorizedHttpException
{
    public function __construct(?string $message = '', \Throwable $previous = null, ?int $code = 0, array $headers = [])
    {
        $challenge = 'Basic realm="Password" charset="UTF-8"';
        parent::__construct($challenge, $message, $previous, $code, $headers);
    }
}
