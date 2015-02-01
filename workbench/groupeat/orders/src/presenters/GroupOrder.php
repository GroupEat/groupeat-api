<?php namespace Groupeat\Orders\Presenters;

use Groupeat\Support\Presenters\Presenter;
use HtmlObject\Table;

class GroupOrder extends Presenter {

    public function presentCreationTime()
    {
        return $this->formatTime($this->created_at);
    }

    public function presentEndingTime()
    {
        return $this->formatTime($this->ending_at);
    }

}
