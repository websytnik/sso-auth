<?php

namespace websytnik\sso\authenticators;

use websytnik\sso\SSOConfig;

class SessionAuthenticator implements AuthenticatorInterface
{
    protected $identity;

    public function can(): bool
    {
        return isset($_COOKIE[SSOConfig::get('cookieName', 'session')]);
    }

    public function auth()
    {
        if (!$this->hasSession()) {
            if (($identity = $this->fetch()) !== null) {
                $_SESSION['sso'] = [
                    'identity' => $identity,
                    'expired' => time() + SSOConfig::get('sessionLifetime', 120)
                ];
            } elseif (isset($_SESSION['sso'])) {
                unset($_SESSION['sso']);
            }
        }
    }

    public function identity()
    {
        return $_SESSION['sso']['identity'] ?? null;
    }

    protected function hasSession(): bool
    {
        if (isset($_SESSION['sso'])) {
            $expired = $_SESSION['sso']['expired'] ?? 0;
            return $expired > time();
        }

        return false;
    }

    protected function fetch()
    {
        try {
            $response =  file_get_contents(SSOConfig::get('server') . '/api/session', false, stream_context_create(['http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'timeout' => 60,
                'content' => http_build_query([
                    'session' => $_COOKIE['session'],
                    'secret' => SSOConfig::get('secret')
                ])]
            ]));
        } catch (\Exception $exception) {
            return null;
        }

        if ($response) {
            return json_decode($response, true);
        }

        return null;
    }
}