<?php
namespace Groupeat\Auth\Services;

use Illuminate\Contracts\Routing\UrlGenerator;

class GetResetPasswordUrl
{
    private $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function call(string $token): string
    {
        return $this->urlGenerator->to("auth/password/reset?token=$token");
    }
}
