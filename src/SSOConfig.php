<?php

namespace websytnik\sso;

class SSOConfig
{
    protected static $config = [];

    public static function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                self::set($k, $v);
            }
        } else {
            self::$config[$key] = $value;
        }
    }

    public static function get($key, $default = null)
    {
        return self::$config[$key] ?? $default;
    }

    public function remove($key)
    {
        unset(self::$config[$key]);
    }
}