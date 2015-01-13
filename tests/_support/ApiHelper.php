<?php namespace Codeception\Module;

class ApiHelper extends \Codeception\Module
{
    public function sendApiGET($path, $params = [])
    {
        $this->sendApi('GET', $path, $params);
    }

    public function sendApiPUT($path, $params = [])
    {
        $this->sendApi('PUT', $path, $params);
    }

    public function sendApiPATCH($path, $params = [])
    {
        $this->sendApi('PATCH', $path, $params);
    }

    public function sendApiPOST($path, $params = [])
    {
        $this->sendApi('POST', $path, $params);
    }

    public function sendApiDELETE($path, $params = [])
    {
        $this->sendApi('DELETE', $path, $params);
    }

    public function sendApi($verb, $path, $params = [])
    {
        $method = 'send'.strtoupper($verb);
        $url = $this->getApiUrl($path);

        $this->getModule('Laravel4')->kernel['groupeat.auth']->logout();

        $this->haveAcceptHeader();
        $this->getModule('REST')->$method($url, $params);
        dump($this->getModule('Laravel4')->kernel['groupeat.auth']->check());
    }

    public function getApiUrl($path)
    {
        return '/'.$this->getModule('Laravel4')->kernel['config']->get('api::prefix').'/'.$path;
    }

    public function seeResponseMessageIs($message)
    {
        $this->getModule('REST')->seeResponseContainsJson(compact('message'));
    }

    public function seeErrorResponse($code, $message)
    {
        $this->getModule('REST')->seeResponseCodeIs($code);
        $this->seeResponseMessageIs($message);
    }

    public function seeErrorsContain($errors)
    {
        $this->getModule('REST')->seeResponseContainsJson(compact('errors'));
    }

    public function haveAcceptHeader()
    {
        $config = $this->getModule('Laravel4')->kernel['config']->get('api::config');
        $value = "application/vnd.{$config['vendor']}.{$config['version']}+{$config['default_format']}";

        return $this->getModule('REST')->haveHttpHeader('Accept', $value);
    }

    public function haveAuthenticationToken($token)
    {
        $this->getModule('REST')->haveHttpHeader('Authorization', "bearer $token");
    }

}
