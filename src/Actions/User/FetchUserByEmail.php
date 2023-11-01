<?php

namespace Merlinpanda\Account\Actions\User;

use Merlinpanda\Account\Models\UserEmail;

/**
 * 根据邮箱获取用户信息
 */
class FetchUserByEmail
{
    public function handle(string $email)
    {
        $user_email = UserEmail::where([
            'email' => $email
        ])->whereNotNull('email_verified_at')->with('user')->first();

        if (!isset($user_email->id)) {
            return null;
        }

        return $user_email->user;
    }
}
