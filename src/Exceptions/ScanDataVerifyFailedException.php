<?php

namespace Merlinpanda\Account\Exceptions;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ScanDataVerifyFailedException extends BadRequestException
{
    public function __construct()
    {
        parent::__construct(__('account::account.bad_request'), ErrorCodes::SCAN_DATA_VERIFY_FAILED, null);
    }
}
