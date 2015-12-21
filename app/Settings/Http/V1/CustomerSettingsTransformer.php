<?php
namespace Groupeat\Settings\Http\V1;

use Groupeat\Settings\Entities\CustomerSettings;
use League\Fractal\TransformerAbstract;

class CustomerSettingsTransformer extends TransformerAbstract
{
    public function transform(CustomerSettings $settings)
    {
        return $settings->toArray();
    }
}
