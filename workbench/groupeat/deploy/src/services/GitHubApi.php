<?php namespace Groupeat\Deploy\Services;

use anlutro\cURL\cURL;
use Closure;

class GitHubApi extends cURL {

    const USER_AGENT = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';

    private $username;
    private $password;
    private $output;
    private $onError;


    function __construct($username, $password, $output, Closure $onError)
    {
        $this->username = $username;
        $this->password = $password;
        $this->output = $output;
        $this->onError = $onError;
    }

    public function getEmail()
    {
        $emails = $this->send('get', 'user/emails');

        foreach ($emails as $email)
        {
            if ($email->primary)
            {
                return $email->email;
            }
        }

        throw new \RuntimeException("Cannot find GitHub email.");
    }

    public function addSSHkey($key, $title)
    {
        $parts = explode(' ', $key);
        array_pop($parts);
        $keyWithoutEmail = implode(' ', $parts);

        $existingKeys = $this->send('get', 'user/keys');

        foreach ($existingKeys as $existingKey)
        {
            if ($existingKey->key == $keyWithoutEmail && $existingKey->title == $title)
            {
                $this->output->line("$title SSH key already exists in GitHub");
                return;
            }
            if (
                ($existingKey->key != $keyWithoutEmail && $existingKey->title == $title) ||
                ($existingKey->key == $keyWithoutEmail && $existingKey->title != $title)
            )
            {
                $this->output->line("Deleting obsolete $title SSH key from GitHub");
                $this->send('delete', "user/keys/$existingKey->id");
            }
        }

        $this->output->line("Adding $title SSH key to GitHub");
        $this->send('post', 'user/keys', compact('title', 'key'));
    }

    public function addOAuthToken($title)
    {
        $authorizations = $this->send('get', 'authorizations');

        foreach ($authorizations as $authorization)
        {
            if ($authorization->app->name == "$title (API)")
            {
                $this->output->line("$title OAuth token already exists in GitHub");
                return $authorization->token;
            }
        }

        $this->output->line("Adding $title OAuth token to GitHub");
        $response = $this->send('post', 'authorizations', [
            'scopes' => [],
            'note' => $title,
        ]);

        return $response->token;
    }

    public function send($HTTPverb, $path, $data = [])
    {
        $onError = $this->onError;
        $url = "https://api.github.com/$path";
        $HTTPverb = strtolower($HTTPverb);

        $request = (new cURL)->newRequest($HTTPverb, $url, $data)
            ->setHeader('content-type', 'application/json')
            ->setOptions([
                CURLOPT_USERPWD => "$this->username:$this->password",
                CURLOPT_USERAGENT => static::USER_AGENT,
            ]);

        if (!empty($data) && $HTTPverb != 'get')
        {
            $request->setEncoding($request::ENCODING_JSON);
        }

        $response = $request->send();

        if ($response->statusCode == 401)
        {
            $this->output->error("Wrong GitHub password for $this->username");
            $onError();
        }

        if ($response->statusCode == 403)
        {
            $this->output->error("Too many GitHub requests with invalid credentials for $this->username");
            $onError();
        }

        $this->output->line("$url: $response->statusCode");

        return json_decode($response->body);
    }

}
