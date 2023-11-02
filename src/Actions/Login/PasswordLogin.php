<?php

namespace Merlinpanda\Account\Actions\Login;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Merlinpanda\Account\Models\User;
use Merlinpanda\Account\Models\UserCellphone;
use Merlinpanda\Account\Models\UserEmail;
use Merlinpanda\Account\Exceptions\AccountOrPasswordNotMatchException;
use Illuminate\Support\Facades\Hash;
use Merlinpanda\Rbac\Exceptions\AccessDeniedException;
use Merlinpanda\Rbac\Rbac;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class PasswordLogin
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
     * @param string $method
     * @param string $account
     * @param string $password
     * @param string $app_key
     * @return User
     */
    public function handle(
        string $method,
        string $account,
        string $password,
        string $app_key
    ): User
    {
        $user_id = $this->fetchUserId($method, $account);

        try{
            $user = User::where(['id' => $user_id, 'status' => 'NORMAL'])->firstOrFail();
        }catch (ModelNotFoundException $e) {
            throw new AccountOrPasswordNotMatchException(__('account::account.failed.password', [
                'attribute' => strtolower($method)
            ]));
        }

        $user_role_value = Rbac::getUserRoleValueByAppKey($user_id, $app_key);
        if (!$user_role_value) {
            throw new AccessDeniedException();
        }

        if (Hash::check($password, $user->password)) {
            return $user;
        }

        throw new AccountOrPasswordNotMatchException(__('account::account.failed.password', [
            'attribute' => strtolower($method)
        ]));
    }

    /**
     * 获取用户ID
     *
     * @param $method
     * @param $account
     * @return mixed
     */
    public function fetchUserId($method, $account)
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
