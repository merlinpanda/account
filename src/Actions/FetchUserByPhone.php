<?php

namespace Merlinpanda\Account\Actions;

use Merlinpanda\Account\Models\UserCellphone;

/**
 * 根据手机号获取用户信息
 *
 */
class FetchUserByPhone
{
    public function handle(string $cellphone, string $country_code)
    {
        $user_phone = UserCellphone::where([
            'cellphone' => phone($cellphone, $country_code)
        ])->whereNotNull('phone_verified_at')->with('user')->first();

        if (!isset($user_phone->id)) {
            return null;
        }

        return $user_phone->user;
    }
}
