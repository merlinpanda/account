<?php

namespace Merlinpanda\Account\Exceptions;

class AbnormalLoginException extends \Exception
{
    public function __construct($message = "")
    {
        $errorMsg = $message ?: __("account::account.auth.abnormal_login");
        parent::__construct($errorMsg, ErrorCodes::ABNORMAL_LOGIN);
    }
}
