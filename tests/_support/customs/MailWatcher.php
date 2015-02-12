<?php namespace Codeception\Module;

use Symfony\Component\DomCrawler\Crawler;

class MailWatcher extends \Codeception\Module {

    /**
     * @var Swift_Message|null
     */
    private $lastMail;


    public function _before()
    {
        $this->lastMail = null;

        $this->getModule('Laravel4')->kernel['events']->listen('mailer.sending', function($message)
        {
            $this->lastMail = $message;

            $parts = explode('Content-Type: text/html;', (string) $message);
            $mailWithoutHtml = $parts[0];

            $this->debugSection('Mail', $mailWithoutHtml);
        });
    }

    public function grabLastMailCrawlableBody()
    {
        return new Crawler($this->grabLastMail()->getBody());
    }

    public function grabHrefInLinkByIdInLastMail($id)
    {
        return trim($this->grabLastMailCrawlableBody()->filter("#$id")->attr('href'));
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
