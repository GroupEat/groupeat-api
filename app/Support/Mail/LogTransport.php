<?php
namespace Groupeat\Support\Mail;

use Illuminate\Mail\Transport\LogTransport as IlluminateLogTransport;
use Swift_Mime_MimeEntity;

class LogTransport extends IlluminateLogTransport
{
    protected function getMimeEntityString(Swift_Mime_MimeEntity $entity)
    {
        return explode('Content-Type: text/html;', (string) $entity)[0];
    }
}
