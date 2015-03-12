<?php
namespace Codeception\Module;

use Symfony\Component\DomCrawler\Crawler;

class MailWatcher extends \Codeception\Module
{
    /**
     * @var \Swift_Message
     */
    private $lastMail;

    public function _before()
    {
        $this->lastMail = null;
        $this->plainTextPart = null;

        $this->getModule('Laravel5')->app['events']->listen('mailer.sending', function ($mail) {
            $this->lastMail = $mail;
            $this->debugSection('Mail', $this->getPlainText($mail));
        });
    }

    public function assertLastMailContains($needle)
    {
        $lastMail = $this->grabLastMail();
        $this->assertContains($needle, $lastMail->getBody());
    }

    public function grabLastMailCrawlableBody()
    {
        return new Crawler($this->grabLastMail()->getBody());
    }

    public function grabHrefInLinkByIdInLastMail($id)
    {
        $href = trim($this->grabLastMailCrawlableBody()->filter("#$id")->attr('href'));

        $this->debugSection('Href in Mail', $href);

        return $href;
    }

    public function grabLastMailRecipient()
    {
        return array_keys($this->grabLastMail()->getTo())[0];
    }

    public function grabLastMailId()
    {
        return trim($this->grabLastMailCrawlableBody()->filter('table')->first()->attr('id'));
    }

    public function grabLastMailBody()
    {
        return $this->grabLastMail()->getBody();
    }

    /**
     * @return Swift_Message
     */
    public function grabLastMail()
    {
        if (empty($this->lastMail)) {
            \PHPUnit_Framework_Assert::fail("No mail was sent.");
        }

        return $this->lastMail;
    }

    /**
     * @param \Swift_Message $mail
     *
     * @return string
     */
    private function getPlainText(\Swift_Message $mail)
    {
        return explode('Content-Type: text/html;', (string) $mail)[0];
    }
}
