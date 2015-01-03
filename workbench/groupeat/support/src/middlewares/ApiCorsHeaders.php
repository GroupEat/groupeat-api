<?php namespace Groupeat\Support\Middlewares;

use Illuminate\Foundation\Application;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

class ApiCorsHeaders implements HttpKernelInterface {

    /**
     * @var Application
     */
    protected $app;


    /**
     * @param  Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Add headers to the API responses to enable CORS on mobile app.
     *
     * @param  Request $request
     * @param  int     $type
     * @param  bool    $catch
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $response = $this->app->handle($request, $type, $catch);

        if ($this->isApi($request))
        {
            $headers = [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS',
                'Access-Control-Allow-Headers' => 'Origin, Content-Type, Accept, Authorization, X-Request-With',
                'Access-Control-Allow-Credentials' => 'true',
            ];

            $replace = true;

            foreach ($headers as $key => $value)
            {
                $response->headers->set($key, $value, $replace);
            }
        }

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function isApi(Request $request)
    {
        $apiPrefix = '/'.$this->app['config']->get('api::prefix').'/';

        return starts_with($request->getPathInfo(), $apiPrefix);
    }

}
