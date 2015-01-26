<?php namespace Groupeat\Support\Services;

use Closure;
use Dingo\Api\Routing\Router;
use Groupeat\Support\Exceptions\BadRequest;
use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Translation\Translator;

class Locale {

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var array
     */
    private $availableLocales;

    /**
     * @var string
     */
    private $locale;


    /**
     * @param Router $router
     * @param array $availableLocales
     */
    public function __construct(Router $router, Translator $translator, array $availableLocales)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->availableLocales = $availableLocales;
        $this->locale = $availableLocales[0];
    }

    /**
     * @return string
     */
    public function get()
    {
        return $this->locale;
    }

    /**
     * @param $locale
     *
     * @return $this
     */
    public function set($locale)
    {
        $this->assertAvailable($locale);

        $this->locale = $locale;

        return $this;
    }

    /**
     * @param $locale
     *
     * @return $this
     */
    public function setForWholeRequest($locale)
    {
        $this->set($locale);

        $this->translator->setLocale($locale);

        return $this;
    }

    /**
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param callable      $callback
     * @param string|null   $locale If null, the detected locale will be used
     *
     * @return mixed
     */
    public function executeWithUserLocale(Closure $callback, $locale = null)
    {
        $locale = $locale ?: $this->get();

        $this->assertNotNull($locale);

        $previousLocale = $this->translator->getLocale();
        $this->translator->setLocale($locale);

        $res = $callback();

        $this->translator->setLocale($previousLocale);

        return $res;
    }

    /**
     * @param $locale
     */
    public function assertAvailable($locale)
    {
        $this->assertNotNull($locale);

        if (!in_array($locale, $this->availableLocales))
        {
            $availableLocalesTest = implode(', ', $this->availableLocales);

            throw new BadRequest(
                'unavailableLocale',
                "The locale $locale should belong to [$availableLocalesTest]."
            );
        }
    }

    /**
     * @param Request $request
     */
    public function detectAndSetIfNeeded(Request $request)
    {
        $this->detect($request);

        if (!$this->router->isApiRequest($request))
        {
            $this->setForWholeRequest($this->get());
        }
    }

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function detect(Request $request)
    {
        $this->detectFromAcceptLanguageHeader($request);

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function detectFromAcceptLanguageHeader(Request $request)
    {
        $languageHeader = $request->headers->get('accept-language');

        if ($languageHeader)
        {
            $languages = explode(';', $languageHeader);

            foreach ($languages as $language)
            {
                foreach ($this->availableLocales as $locale)
                {
                    if (str_contains($language, $locale))
                    {
                        $this->set($locale);

                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function assertNotNull($locale)
    {
        if (empty($locale))
        {
            throw new BadRequest(
                'noValidLocaleGiven',
                "A valid locale must be given."
            );
        }
    }

}
