<?php
namespace Codeception\Module;

use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;
use Swift_Message;

class MailWatcher extends \Codeception\Module
{
    /**
     * @var Collection
     */
    private $mails;

    public function _before()
    {
        $this->flush();

        $this->getModule('Laravel5')->app['events']->listen('mailer.sending', function ($mail) {
            $this->mails->push($mail);
            $this->debugSection('Mail', $this->getPlainText($mail));
        });
    }

    public function flush()
    {
        $this->mails = collect();
    }

    /**
     * @param string $needle
     */
    public function assertFirstMailContains($needle)
    {
        $this->assertMailContains($this->grabFirstMail(), $needle);
    }

    public function grabFirstMailCrawlableBody()
    {
        return $this->grabMailCrawlableBody($this->grabFirstMail());
    }

    /**
     * @param string $id
     *
     * @return string
     */
    public function grabHrefInLinkByIdInFirstMail($id)
    {
        return $this->grabHrefInLinkByIdInMail($this->grabFirstMail(), $id);
    }

    public function grabFirstMailRecipient()
    {
        return $this->grabMailRecipient($this->grabFirstMail());
    }

    public function grabFirstMailId()
    {
        return $this->grabMailId($this->grabFirstMail());
    }

    public function grabFirstMailBody()
    {
        return $this->grabFirstMail()->getBody();
    }

    /**
     * @param Swift_Message $mail
     * @param string        $needle
     */
    public function assertMailContains(Swift_Message $mail, $needle)
    {
        $this->assertContains($needle, $mail->getBody());
    }

    public function grabMailId(Swift_Message $mail)
    {
        return trim($this->grabMailCrawlableBody($mail)->filter('table')->first()->attr('id'));
    }

    public function grabMailCrawlableBody(Swift_Message $mail)
    {
        return new Crawler($mail->getBody());
    }

    /**
     * @param Swift_Message $mail
     * @param string        $id
     *
     * @return string
     */
    public function grabHrefInLinkByIdInMail(Swift_Message $mail, $id)
    {
        $href = trim($this->grabMailCrawlableBody($mail)->filter("#$id")->attr('href'));

        $this->debugSection('Href in Mail', $href);

        return $href;
    }

    public function grabMailRecipient(Swift_Message $mail)
    {
        return array_keys($mail->getTo())[0];
    }

    /**
     * @param string $id
     *
     * @return Swift_Message
     */
    public function grabMailById($id)
    {
        $mails = $this->grabMails()->filter(function (Swift_Message $mail) use ($id) {
            return $this->grabMailId($mail) == $id;
        });

        if ($mails->isEmpty()) {
            \PHPUnit_Framework_Assert::fail("No mail with id '$id' was sent.");
        }

        return $mails->first();
    }

    public function grabMails()
    {
        if ($this->mails->isEmpty()) {
            \PHPUnit_Framework_Assert::fail('No mail was sent.');
        }

        return $this->mails;
    }

    /**
     * @return Swift_Message
     */
    public function grabFirstMail()
    {
        if (empty($this->mails->first())) {
            \PHPUnit_Framework_Assert::fail('No mail was sent.');
        }

        return $this->mails->first();
    }

    /**
     * @param Swift_Message $mail
     *
     * @return string
     */
    private function getPlainText(Swift_Message $mail)
    {
        return explode('Content-Type: text/html;', (string) $mail)[0];
    }
}
