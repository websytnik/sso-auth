<?php


namespace websytnik\sso\authenticators;


interface AuthenticatorInterface
{
    public function can(): bool;
    public function auth();
    public function identity();
}