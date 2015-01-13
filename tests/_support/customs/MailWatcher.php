<?php namespace Codeception\Module;

use Codeception\Util\Debug;
use Symfony\Component\DomCrawler\Crawler;

class MailWatcher extends \Codeception\Module
{
    /**
     * @var Swift_Message|null
     */
    private $lastMail;


    public function _before()
    {
        $this->lastMail = null;

        $this->getModule('Laravel4')->kernel['events']->listen('mailer.sending', function($message)
        {
            Debug::debug('[Mail] '.$message);
            $this->lastMail = $message;
        });
    }

    public function grabLastMailCrawlableBody()
    {
        return new Crawler($this->grabLastMail()->getBody());
    }

    public function grabLastMailBody()
    {
        return $this->grabLastMail()->getBody();
    }

    public function grabLastMail()
    {
        if (empty($this->lastMail))
        {
            \PHPUnit_Framework_Assert::fail("No mail was sent.");
        }

        return $this->lastMail;
    }

}
