<?php

namespace Merlinpanda\Account\Contracts;

use Merlinpanda\Account\Models\User;

interface AbnormalLogin
{
    public function isAbnormalLogin(User $user): bool;
}
