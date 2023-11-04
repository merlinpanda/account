<?php

namespace Merlinpanda\Account\Actions\Login;

use Illuminate\Support\Facades\Cache;
use Merlinpanda\Account\Actions\Senders\EmailNotificationSender;
use Merlinpanda\Account\Actions\Senders\SmsNotificationSender;
use Merlinpanda\Account\Contracts\AbstractAccountLogin;
use Merlinpanda\Rbac\Exceptions\AccessDeniedException;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class CodeLogin extends AbstractAccountLogin
{
    const METHOD_SENDERS = [
        self::METHOD_EMAIL => EmailNotificationSender::class,
        self::METHOD_CELLPHONE => SmsNotificationSender::class
    ];

    const LOCK_TIME_LONG = 60; // 60s

    const CODE_CACHE_MINUTES = 5;  // 分钟

    const CODE_CACHE_TIME_LONG = self::CODE_CACHE_MINUTES * 60; // 秒

    public function handle(string $method, string $account, string $code, string $app_key)
    {
        //
    }

    public function sendCode(string $method, string $account)
    {
        $method = strtoupper($method);
        if (! in_array($method, self::ALLOWED_METHODS, true)) {
            throw new InvalidParameterException(__('account::account.method_not_allowed', [
                'method' => $method
            ]));
        }

        // 判断当前账号是否处于锁定状态
        $lock_cache_key = $this->lockCacheKey($method, $account);
        if ($this->isLocking($lock_cache_key)) {
            throw new AccessDeniedException(__("account::account.frequent_operations"));
        }

        // 生成验证码 并缓存
        $code = mt_rand(100000, 999999);
        $code_cache_key = $this->codeCacheKey($method, $account);
        Cache::put($code_cache_key, $code, self::CODE_CACHE_TIME_LONG);

        // 发送短信或邮件
        $sender = new self::METHOD_SENDERS[$method];
        $result = $sender->send();

        // 记录发送内容

        // 锁定，防止操作频繁
        $this->lock($lock_cache_key);
    }

    /**
     * @param string $method
     * @param string $account
     * @return string
     */
    private function codeCacheKey(string $method,string $account): string
    {
        return sprintf("LOGIN_CODE:METHOD_%s:ACCOUNT_%s", $method, $account);
    }

    /**
     * @param string $method
     * @param string $account
     * @return string
     */
    private function lockCacheKey(string $method,string $account): string
    {
        return sprintf("LOGIN_CODE_LOCK:METHOD_%s:ACCOUNT_%s", $method, $account);
    }

    /**
     * @param string $cache_key
     * @return bool
     */
    private function isLocking(string $cache_key): bool
    {
        return Cache::has($cache_key);
    }

    /**
     * @param string $cache_key
     * @return void
     */
    private function lock(string $cache_key)
    {
        Cache::put($cache_key, 1, self::LOCK_TIME_LONG);
    }
}
