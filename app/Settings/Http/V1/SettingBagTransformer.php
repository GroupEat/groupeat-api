<?php
namespace Groupeat\Settings\Http\V1;

use Groupeat\Settings\Support\SettingBag;
use League\Fractal\TransformerAbstract;

class SettingBagTransformer extends TransformerAbstract
{
    public function transform(SettingBag $settingBag)
    {
        return $settingBag->all();
    }
}
