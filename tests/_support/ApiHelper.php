<?php namespace Codeception\Module;

class ApiHelper extends \Codeception\Module
{
    public function sendRegistrationRequest(
        $email = 'user@ensta.fr',
        $password = 'password',
        $resource = 'customers',
        $locale = 'fr'
    )
    {
        $this->sendApiPost($resource, compact('email', 'password', 'locale'));
        $this->getModule('REST')->seeResponseCodeIs(201);
        $id = $this->grabDataFromResponse('id');
        $token = $this->grabDataFromResponse('token');
        $type = $this->grabDataFromResponse('type');

        return [$token, $id, $type];
    }

    public function sendApiGetWithToken($token, $path, $params = [])
    {
        $this->sendApiWithToken($token, 'GET', $path, $params);
    }

    public function sendApiPutWithToken($token, $path, $params = [])
    {
        $this->sendApiWithToken($token, 'PUT', $path, $params);
    }

    public function sendApiPatchWithToken($token, $path, $params = [])
    {
        $this->sendApiWithToken($token, 'PATCH', $path, $params);
    }

    public function sendApiPostWithToken($token, $path, $params = [])
    {
        $this->sendApiWithToken($token, 'POST', $path, $params);
    }

    public function sendApiDeleteWithToken($token, $path, $params = [])
    {
        $this->sendApiWithToken($token, 'DELETE', $path, $params);
    }

    public function sendApiGet($path, $params = [])
    {
        $this->sendApi('GET', $path, $params);
    }

    public function sendApiPut($path, $params = [])
    {
        $this->sendApi('PUT', $path, $params);
    }

    public function sendApiPatch($path, $params = [])
    {
        $this->sendApi('PATCH', $path, $params);
    }

    public function sendApiPost($path, $params = [])
    {
        $this->sendApi('POST', $path, $params);
    }

    public function sendApiDelete($path, $params = [])
    {
        $this->sendApi('DELETE', $path, $params);
    }

    public function sendApiWithToken($token, $verb, $path, $params = [])
    {
        $this->haveAuthenticationToken($token);
        $this->sendApi($verb, $path, $params);
    }

    public function sendApi($verb, $path, $params = [])
    {
        $method = 'send'.strtoupper($verb);
        $url = $this->getApiUrl($path);

        $this->getModule('Laravel4')->kernel['groupeat.auth']->logout();

        $this->haveAcceptHeader();
        $this->getModule('REST')->$method($url, $params);
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

    public function grabDataFromResponse($path)
    {
        return $this->getModule('REST')->grabDataFromJsonResponse("data.$path");
    }

    public function watchEmailSending()
    {
        $this->getModule('Laravel4');
    }

    public function haveAcceptHeader()
    {
        $config = $this->getModule('Laravel4')->kernel['config']->get('api::config');
        $accept = "application/vnd.{$config['vendor']}.{$config['version']}+{$config['default_format']}";

        $this->getModule('REST')->haveHttpHeader('Accept', $accept);
    }

    public function haveAuthenticationToken($token)
    {
        $this->getModule('REST')->haveHttpHeader('Authorization', "bearer $token");
    }

}
