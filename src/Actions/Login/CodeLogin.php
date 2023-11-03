<?php

namespace Merlinpanda\Account\Actions\Login;

use Merlinpanda\Account\Actions\SendVerifyCode;

class CodeLogin
{
    protected $send_verify_code;

    public function __construct(SendVerifyCode $sendVerifyCode)
    {
        $this->send_verify_code = $sendVerifyCode;
    }

    public function handle()
    {

    }
}
