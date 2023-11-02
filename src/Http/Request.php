<?php

namespace Merlinpanda\Account\Http;

use Illuminate\Http\Request as HttpRequest;

class Request extends HttpRequest
{
    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    /**
     * 角色权限
     * @return void
     */
    public function roleValue()
    {
        return $this->user('api')->payload();
    }

    private function appRoleKey()
    {
        return strtolower($this->appKey()) . '_role';
    }

    /**
     * @return array|string|null
     */
    public function appKey()
    {
        return $this->header('App-Key') ?: null;
    }
}
