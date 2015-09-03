<?php
namespace Groupeat\Auth\Http\V1;

use Groupeat\Auth\Auth;
use Groupeat\Auth\Entities\Interfaces\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'type' => app(Auth::class)->shortTypeOf($user),
            'token' => $user->credentials->token,
            'activated' => $user->credentials->isActivated(),
        ];
    }
}
