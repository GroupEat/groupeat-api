<?php
namespace Groupeat\Settings\Events;

use Groupeat\Settings\Entities\CustomerSettings;
use Groupeat\Support\Events\Abstracts\Event;

class CustomerHasUpdatedItsSettings extends Event
{
    private $settings;

    public function __construct(CustomerSettings $settings)
    {
        $this->$settings = $settings;
    }

    public function getSettings()
    {
        return $this->settings;
    }
}
