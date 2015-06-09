<?php
namespace Codeception\Module;

use Groupeat\Auth\Auth;

class ApiHelper extends \Codeception\Module
{
    public function amAnActivatedCustomer()
    {
        list($token, $id) = $this->sendRegistrationRequest();
        $activationLink = $this->getModule('MailWatcher')->grabHrefInLinkByIdInFirstMail('activation-link');
        list(, $activationToken) = explode("token=", $activationLink);
        $this->sendApiPost('auth/activationTokens', ['token' => $activationToken]);

        return [$token, $id];
    }

    public function sendRegistrationRequest(
        $email = null,
        $password = 'password',
        $resource = 'customers',
        $locale = 'fr'
    ) {
        if (is_null($email)) {
            $email = uniqid().'@ensta.fr';
        }

        $this->sendApiPost($resource, compact('email', 'password', 'locale'));
        $id = $this->grabDataFromResponse('id');
        $token = $this->grabDataFromResponse('token');
        $type = $this->grabDataFromResponse('type');

        return [$token, $id, $type];
    }

    public function createGroupOrder()
    {
        list($token, $customerId) = $this->amAnActivatedCustomer();

        $this->sendApiGetWithToken(
            $token,
            'groupOrders?joinable=1&around=1&latitude=48.716941&longitude=2.239171&include=restaurant'
        );

        $groupOrders = $this->grabDataFromResponse('');

        if (!empty($groupOrders)) {
            $groupOrderId = $groupOrders[0]['id'];
            $restaurantId = $groupOrders[0]['restaurant']['data']['id'];
        } else {
            $groupOrderId = null;
            $this->sendApiGetWithToken($token, 'restaurants?opened=1&around=1&latitude=48.716941&longitude=2.239171');
            $restaurants = $this->grabDataFromResponse();
            $restaurantId = $restaurants[0]['id'];
        }

        $this->sendApiGetWithToken($token, "restaurants/$restaurantId");
        $restaurantCapacity = $this->grabDataFromResponse('deliveryCapacity');
        $this->assertGreaterThan(1, $restaurantCapacity);
        $this->sendApiGetWithToken($token, "restaurants/$restaurantId/products?include=formats");
        $productFormatId = last(last($this->grabDataFromResponse())['formats']['data'])['id'];
        $productFormats = [$productFormatId => 1];

        $orderDetails = [
            'foodRushDurationInMinutes' => 30,
            'productFormats' => $productFormats,
            'street' => "Allée des techniques avancées",
            'details' => "Bâtiment A, chambre 200",
            'latitude' => 48.716941,
            'longitude' => 2.239171,
        ];

        if (!is_null($groupOrderId)) {
            $orderDetails['groupOrderId'] = $groupOrderId;
        }

        $this->sendApiPostWithToken($token, 'orders', $orderDetails);
        $orderId = $this->grabDataFromResponse('id');

        return [$token, $orderId, $restaurantCapacity, $orderDetails, $customerId];
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
        $verb = strtoupper($verb);
        $url = $this->getApiUrl($path);

        $this->getModule('MailWatcher')->flush();
        $this->getModule('Laravel5')->app[Auth::class]->logout();

        $this->haveAcceptHeader();
        $body = $verb != 'GET' ? json_encode($params) : null;
        $RESTmodule = $this->getModule('REST');
        $client = $RESTmodule->client;

        foreach ($RESTmodule->headers as $header => $val) {
            $header = str_replace('-', '_', strtoupper($header));
            $client->setServerParameter("HTTP_$header", $val);

            if (strtolower($header) == 'host') {
                $client->setServerParameter("HTTP_HOST", $val);
            }

            if ($RESTmodule->isFunctional and $header == 'CONTENT_TYPE') {
                $client->setServerParameter($header, $val);
            }
        }

        $this->debugSection("Request", "$verb $url ".$body);
        $this->debugSection("Headers", json_encode($RESTmodule->headers));
        $client->request($verb, $url, [], [], [], $body);
        $RESTmodule->response = (string) $client->getInternalResponse()->getContent();
        $this->debugSection("Response", $RESTmodule->response);

        if (count($client->getInternalRequest()->getCookies())) {
            $this->debugSection('Cookies', $client->getInternalRequest()->getCookies());
        }

        $this->debugSection("Headers", $client->getInternalResponse()->getHeaders());
        $this->debugSection("Status", $client->getInternalResponse()->getStatus());
    }

    public function getApiUrl($path)
    {
        return "/api/$path";
    }

    public function seeResponseErrorKeyIs($errorKey)
    {
        $this->getModule('REST')->seeResponseContainsJson(['data' => ['errorKey' => $errorKey]]);
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

    public function grabDataFromResponse($path = '')
    {
        if ($path) {
            $path = ".$path";
        }

        return $this->getModule('REST')->grabDataFromJsonResponse('data'.$path);
    }

    public function haveAcceptHeader()
    {
        $this->getModule('REST')->haveHttpHeader('Accept', 'application/vnd.groupeat.v1+json');
    }

    public function runArtisan($command, array $parameters = [])
    {
        $output = artisan($command, $parameters);

        $this->debugSection("Artisan $command", $output);
    }
}
