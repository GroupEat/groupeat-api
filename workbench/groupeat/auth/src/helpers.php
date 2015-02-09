<?php

if (!function_exists('generateAuthTokenFor'))
{
    /**
     * @param \Groupeat\Auth\Entities\UserCredentials $userCredentials
     * @param int|null                                $durationInMinutes
     *
     * @return string
     */
    function generateAuthTokenFor(\Groupeat\Auth\Entities\UserCredentials $userCredentials, $durationInMinutes = null)
    {
        return app('GenerateAuthTokenService')->forUser($userCredentials, $durationInMinutes);
    }
}
