<?php

namespace Merlinpanda\Account\Contracts;

interface AbnormalUser
{
    /**
     * 是否绑定账号
     *
     * @return bool
     */
    public function doesNotHaveAccount(): bool;

    /**
     * 长时间未登录
     *
     * @return bool
     */
    public function isLongTimeNotLogin(): bool;

    /**
     * 新客户端登录
     *
     * @return bool
     */
    public function isNewClientLogin(): bool;

    /**
     * 不是常在地区
     *
     * @return bool
     */
    public function isEmergencyArea(): bool;
}
