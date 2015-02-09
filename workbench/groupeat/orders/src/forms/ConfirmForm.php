<?php namespace Groupeat\Orders\Forms;

use Carbon\Carbon;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Forms\Form;

class ConfirmForm extends Form {

    const STEP_IN_MINUTES = 5;

    /**
     * @var Carbon
     */
    private $completedAt;

    /**
     * @var int
     */
    private $maximumPreparationTimeInMinutes;

    /**
     * @var Carbon
     */
    private $latestPreparedTime;

    protected $rules = [
        'preparationTimeInMinutes' => 'required|integer|max:45',
    ];


    public function __construct(GroupOrder $groupOrder, $maximumPreparationTimeInMinutes)
    {
        parent::__construct();

        $this->completedAt = $groupOrder->completed_at;
        $this->maximumPreparationTimeInMinutes = (int) $maximumPreparationTimeInMinutes;
        $this->setLatestPreparedTime();
    }

    protected function add($content)
    {
        return $content
            . '<p>'.trans('orders::confirmation.form.indication').'</p>'
            . $this->former->select('preparedAt')->options($this->getPossibleTimes())
            . $this->submit('confirm', 'warning');
    }

    private function setLatestPreparedTime()
    {
        $latestPreparedTime = $this->completedAt->copy()->addMinutes($this->maximumPreparationTimeInMinutes);
        $remainingMinutes = Carbon::now()->diffInMinutes($latestPreparedTime, false);

        if ($remainingMinutes < 0)
        {
            throw new UnprocessableEntity(
                'maximumPreparationTimeAlreadyExceeded',
                "The preparation should have ended before $latestPreparedTime."
            );
        }

        $this->latestPreparedTime = $latestPreparedTime;
    }

    private function getPossibleTimes()
    {
        $offsetWithStep = $this->latestPreparedTime->minute % static::STEP_IN_MINUTES;

        if ($offsetWithStep != 0)
        {
            $availableTimes[] = $this->latestPreparedTime;
            $timeOnStep = $this->latestPreparedTime->copy()->subMinutes($offsetWithStep);
        }
        else
        {
            $timeOnStep = $this->latestPreparedTime;
        }

        while ($timeOnStep->isFuture())
        {
            $availableTimes[] = $timeOnStep;
            $timeOnStep = $timeOnStep->copy()->subMinutes(static::STEP_IN_MINUTES);
        }

        foreach (array_reverse($availableTimes) as $time)
        {
            $options[(string) $time] = formatTime($time);
        }

        return $options;
    }

}
