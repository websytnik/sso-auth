<?php

namespace websytnik\sso;

use Exception;
use websytnik\sso\authenticators\AuthenticatorInterface;
use websytnik\sso\authenticators\SessionAuthenticator;

/**
 * Class SSOAuth
 * @package websytnik\sso
 *
 * @property AuthenticatorInterface $authenticator
 */
class SSOAuth
{
    protected $authenticator;

    public function __construct()
    {
        if (SSOConfig::get('server') === null || SSOConfig::get('secret') === null) {
            throw new Exception('Invalid Configuration');
        }

        if ($this->detectAuthenticator()) {
            $this->authenticator->auth();
        }
    }

    public function getIdentity()
    {
        if ($this->authenticator) {
            return $this->authenticator->identity();
        }

        return null;
    }

    protected function hasCookie() : bool
    {
        return isset($_COOKIE[SSOConfig::get('cookieName', 'session')]);
    }

    protected function detectAuthenticator() : bool
    {
        $authenticators = SSOConfig::get('authenticators', [
            SessionAuthenticator::class,
        ]);

        foreach ($authenticators as $class) {
            $authenticator = new $class();

            if ($authenticator->can()) {
                $this->authenticator = $authenticator;
                return true;
            }
        }

        return false;
    }
}