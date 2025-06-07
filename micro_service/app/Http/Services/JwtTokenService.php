<?php

namespace App\Http\Services;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\Clock\SystemClock;
use Throwable;

class JwtTokenService
{
    protected Configuration $jwt;

    public function __construct()
    {
        $this->jwt = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText(env('JWT_SECRET'))
        );
    }

    public function parse(string $token): ?UnencryptedToken
    {
        try {
            $token = $this->jwt->parser()->parse($token);
            if (!$token instanceof UnencryptedToken) {
                return null;
            }

            $constraints = [
                new SignedWith($this->jwt->signer(), $this->jwt->verificationKey()),
                new LooseValidAt(SystemClock::fromUTC())
            ];

            if (!$this->jwt->validator()->validate($token, ...$constraints)) {
                return null;
            }

            return $token;
        } catch (Throwable) {
            return null;
        }
    }
}
