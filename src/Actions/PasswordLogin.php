<?php

namespace Merlinpanda\Account\Actions;

use Merlinpanda\Account\Models\User;
use Merlinpanda\Account\Models\UserCellphone;
use Merlinpanda\Account\Models\UserEmail;
use Merlinpanda\Account\Contracts\AbnormalLogin;
use Merlinpanda\Account\Contracts\LoginAction;
use Merlinpanda\Account\Exceptions\AbnormalLoginException;
use Merlinpanda\Account\Exceptions\AccountOrPasswordNotMatchException;
use Illuminate\Support\Facades\Hash;
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
    public function handle(string $method, string $account, string $password, array $claims = []): string
    {
        $method = strtoupper($method);

        if (! in_array($method, self::ALLOWED_METHODS, true)) {
            throw new InvalidParameterException(__('account.method_not_allowed', [
                'method' => $method
            ]));
        }

        $method_model = self::METHOD_MODELS[$method];

        $method_user = (new $method_model())->where([strtolower($method) => $account, 'priority' => self::CAN_USE_FOR_LOGIN_PRIORITY])->exists();
        if ($method_user) {
            $user_id = (new $method_model())->where([strtolower($method) => $account, 'priority' => self::CAN_USE_FOR_LOGIN_PRIORITY])->value('user_id');
        } else {
            throw new AccountOrPasswordNotMatchException(__('account.failed.password', [
                'attribute' => strtolower($method)
            ]));
        }

        $user = User::where(['id' => $user_id, 'status' => 'NORMAL'])->first();

        /**
         * 检查是否异常登录
         * 如果出现异常，应改为验证码登录
         */
        $this->isAbnormalLogin($user);

        $this->extraVerify($user);

        if (Hash::check($password, $user->password)) {
            $token = auth('api')->claims($claims)->login($user);

            return $this->formatToken($token);
        }

        throw new AccountOrPasswordNotMatchException(__('account.failed.password', [
            'attribute' => strtolower($method)
        ]));
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
            throw new AbnormalLoginException(__('account.disabled'));
        }

        if ($user->isLongTimeNotLogin()) {
            throw new AbnormalLoginException(__('account.abnormal.long_time'));
        }

        if ($user->isNewClientLogin()) {
            throw new AbnormalLoginException(__('account.abnormal.new_client'));
        }

        if ($user->isEmergencyArea()) {
            throw new AbnormalLoginException(__('account.abnormal.emergency_area'));
        }

        return true;
    }
}
