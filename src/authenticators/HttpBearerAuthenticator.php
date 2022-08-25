<?php


namespace websytnik\sso\authenticators;


use websytnik\sso\SSOConfig;

class HttpBearerAuthenticator implements AuthenticatorInterface
{
    protected $identity;

    public function can(): bool
    {
        return $this->token() !== null;
    }

    public function auth()
    {
        $token = $this->token();

        if ($this->validateToken($token)) {
            $this->identity = $this->parseToken($token);
        }
    }

    public function identity()
    {
        return $this->identity;
    }

    protected function token()
    {
        $headers = getallheaders();

        return $headers[SSOConfig::get('header', 'Authorization')] ?? null;
    }

    protected function validateToken($token): bool
    {
        list($header, $payload, $signature) = explode('.', $token);

        return hash('sha256', $header . '.' . $payload . '.' . SSOConfig::get('jwtSecret')) === $signature;
    }

    protected function parseToken($token)
    {
        return json_decode(base64_decode(explode('.', $token)[1]));
    }
}