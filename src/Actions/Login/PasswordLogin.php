<?php

namespace Merlinpanda\Account\Actions\Login;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Merlinpanda\Account\Contracts\AbstractAccountLogin;
use Merlinpanda\Account\Models\User;
use Merlinpanda\Account\Exceptions\AccountOrPasswordNotMatchException;
use Illuminate\Support\Facades\Hash;
use Merlinpanda\Rbac\Exceptions\AccessDeniedException;
use Merlinpanda\Rbac\Rbac;

class PasswordLogin extends AbstractAccountLogin
{
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
        $user_id = $this->fetchUserIdByAccount($method, $account);

        try{
            $user = User::where(['id' => $user_id, 'status' => User::STATUS_NORMAL])->firstOrFail();
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
}
