<?php namespace Codeception\Module;

use Symfony\Component\DomCrawler\Crawler;

class ApiHelper extends \Codeception\Module {

    public function amAnActivatedCustomer()
    {
        $this->sendApiPost('auth/token', [
            'email' => 'groupeat@ensta.fr',
            'password' => 'groupeat',
        ]);

        $id = $this->grabDataFromResponse('id');
        $token = $this->grabDataFromResponse('token');

        return [$token, $id];
    }

    public function sendRegistrationRequest(
        $email = 'user@ensta.fr',
        $password = 'password',
        $resource = 'customers',
        $locale = 'fr'
    )
    {
        $this->sendApiPost($resource, compact('email', 'password', 'locale'));
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
        $this->getModule('REST')->amBearerAuthenticated($token);
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

    public function seeResponseErrorKeyIs($errorKey)
    {
        $this->getModule('REST')->seeResponseContainsJson(['error_key' => $errorKey]);
    }

    public function seeErrorResponse($code, $errorKey)
    {
        $this->getModule('REST')->seeResponseCodeIs($code);
        $this->seeResponseErrorKeyIs($errorKey);
    }

    public function seeErrorsContain(array $errors)
    {
        $this->getModule('REST')->seeResponseContainsJson(compact('errors'));
    }

    public function seeResponseContainsData(array $data)
    {
        return $this->getModule('REST')->seeResponseContainsJson(compact('data'));
    }

    public function grabDataFromResponse($path)
    {
        if ($path)
        {
            $path = ".$path";
        }

        return $this->getModule('REST')->grabDataFromJsonResponse('data'.$path);
    }

    public function grabCrawlableResponse()
    {
        return new Crawler($this->getModule('REST')->grabResponse());
    }

    public function dontSeeSuccessfulPanel()
    {
        $this->assertNotContains('panel-success', $this->grabPanelClasses());
    }

    public function seeSuccessfulPanel()
    {
        $this->assertContains('panel-success', $this->grabPanelClasses());
    }

    public function grabPanelClasses()
    {
        return explode(' ', $this->grabCrawlableResponse()->filter('#groupeat-panel')->first()->attr('class'));
    }

    public function haveAcceptHeader()
    {
        $config = $this->getModule('Laravel4')->kernel['config']->get('api::config');
        $accept = "application/vnd.{$config['vendor']}.{$config['version']}+{$config['default_format']}";

        $this->getModule('REST')->haveHttpHeader('Accept', $accept);
    }

}
