<?php

namespace Merlinpanda\Account\Actions\User;

abstract class NotificationSender
{
    public function send(): bool
    {
        return true;
    }
}
