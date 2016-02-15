<?php
namespace Codeception\Module;

use Groupeat\Messaging\Events\SmsHasBeenSent;
use Groupeat\Messaging\Values\Sms;

class SmsWatcher extends \Codeception\Module
{
    private $sms;

    public function _before()
    {
        $this->flush();

        $this->getModule('Laravel5')->app['events']->listen(
            SmsHasBeenSent::class,
            function (SmsHasBeenSent $event) {
                $sms = $event->getSms();
                $this->sms->push($sms);
                $this->debugSection('Sms', (string) $sms);
            }
        );
    }

    public function flush()
    {
        $this->sms = collect();
    }

    public function grabFirstSms(): Sms
    {
        $firstSms = $this->sms->first();

        if (empty($firstSms)) {
            \PHPUnit_Framework_Assert::fail('No sms was sent.');
        }

        return $firstSms;
    }

    public function assertNoSmsWasSent()
    {
        $firstSms = $this->sms->first();

        if (!empty($firstSms)) {
            \PHPUnit_Framework_Assert::fail('First sms sent was: ' . $firstSms);
        }
    }
}
