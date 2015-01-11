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

        $this->haveAcceptHeader();
        $this->getModule('Rest')->$method($url, $params);
    }

    public function getApiUrl($path)
    {
        return '/'.$this->getModule('Laravel4')->kernel['config']->get('api::prefix').'/'.$path;
    }

    public function seeResponseMessageIs($message)
    {
        $this->getModule('Rest')->seeResponseContainsJson(compact('message'));
    }

    public function seeErrorResponse($code, $message)
    {
        $this->getModule('Rest')->seeResponseCodeIs($code);
        $this->seeResponseMessageIs($message);
    }

    public function haveAcceptHeader()
    {
        $config = $this->getModule('Laravel4')->kernel['config']->get('api::config');
        $value = "application/vnd.{$config['vendor']}.{$config['version']}+{$config['default_format']}";

        return $this->getModule('Rest')->haveHttpHeader('Accept', $value);
    }

}
