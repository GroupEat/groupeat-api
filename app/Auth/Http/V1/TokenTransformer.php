<?php
namespace Groupeat\Auth\Http\V1;

use Auth;
use Groupeat\Auth\Entities\Interfaces\User;
use League\Fractal\TransformerAbstract;

class TokenTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'type' => Auth::shortTypeOf($user),
            'token' => (string) $user->credentials->token,
            'activated' => $user->credentials->isActivated(),
        ];
    }
}
