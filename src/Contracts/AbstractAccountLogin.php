<?php

namespace Merlinpanda\Account\Contracts;

use Merlinpanda\Account\Exceptions\AccountOrPasswordNotMatchException;
use Merlinpanda\Account\Models\UserCellphone;
use Merlinpanda\Account\Models\UserEmail;
use Symfony\Component\Routing\Exception\InvalidParameterException;

abstract class AbstractAccountLogin
{
    const METHOD_EMAIL = "EMAIL";

    const METHOD_CELLPHONE = "CELLPHONE";

    const ALLOWED_METHODS = [self::METHOD_EMAIL, self::METHOD_CELLPHONE];

    const CAN_USE_FOR_LOGIN_PRIORITY = "PRIMARY";

    const METHOD_MODELS = [
        self::METHOD_EMAIL => UserEmail::class,
        self::METHOD_CELLPHONE => UserCellphone::class,
    ];

    /**
     * 获取用户ID
     *
     * @param $method
     * @param $account
     * @return mixed
     */
    public function fetchUserIdByAccount($method, $account)
    {
        $method = strtoupper($method);

        if (! in_array($method, self::ALLOWED_METHODS, true)) {
            throw new InvalidParameterException(__('account::account.method_not_allowed', [
                'method' => $method
            ]));
        }

        $method_model = self::METHOD_MODELS[$method];

        $method_user = (new $method_model())->where([strtolower($method) => $account, 'priority' => self::CAN_USE_FOR_LOGIN_PRIORITY])->exists();
        if ($method_user) {
            return (new $method_model())->where([strtolower($method) => $account, 'priority' => self::CAN_USE_FOR_LOGIN_PRIORITY])->value('user_id');
        } else {
            throw new AccountOrPasswordNotMatchException(__('account::account.failed.password', [
                'attribute' => strtolower($method)
            ]));
        }
    }
}
