<?php

namespace Merlinpanda\Account\Actions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Merlinpanda\Account\Models\User;
use Merlinpanda\Account\Models\UserCellphone;
use Merlinpanda\Account\Models\UserEmail;
use Merlinpanda\Account\Contracts\AbnormalLogin;
use Merlinpanda\Account\Contracts\LoginAction;
use Merlinpanda\Account\Exceptions\AbnormalLoginException;
use Merlinpanda\Account\Exceptions\AccountOrPasswordNotMatchException;
use Illuminate\Support\Facades\Hash;
use Merlinpanda\Rbac\Exceptions\AccessDeniedException;
use Merlinpanda\Rbac\Rbac;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class PasswordLogin extends LoginAction implements AbnormalLogin
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
     * @return string
     * @throws AbnormalLoginException
     */
    public function handle(string $method, string $account, string $password, string $app_key, array $claims = []): string
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

        /**
         * 检查是否异常登录
         * 如果出现异常，应改为验证码登录
         */
        $this->isAbnormalLogin($user);

        $this->extraVerify($user);

        if (Hash::check($password, $user->password)) {
            $claims["role_value"] = $user_role_value;
            $token = auth('api')->claims($claims)->login($user);

            return $this->formatToken($token);
        }

        throw new AccountOrPasswordNotMatchException(__('account::account.failed.password', [
            'attribute' => strtolower($method)
        ]));
    }

    private function fetchUserId($method, $account)
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

    /**
     * 自定义其他认证
     *
     * @return void
     */
    public function extraVerify($user)
    {
        // Other verify
    }

    /**
     * 是否异常
     *
     * @param User $user
     * @return bool
     * @throws AbnormalLoginException
     */
    public function isAbnormalLogin(User $user): bool
    {
        if (!isset($user->id)) {
            throw new AbnormalLoginException(__('account::account.disabled'));
        }

        if ($user->isLongTimeNotLogin()) {
            throw new AbnormalLoginException(__('account::account.abnormal.long_time'));
        }

        if ($user->isNewClientLogin()) {
            throw new AbnormalLoginException(__('account::account.abnormal.new_client'));
        }

        if ($user->isEmergencyArea()) {
            throw new AbnormalLoginException(__('account::account.abnormal.emergency_area'));
        }

        return true;
    }
}
