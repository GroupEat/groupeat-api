<?php
namespace Groupeat\Support\Services;

use Closure;
use Groupeat\Support\Exceptions\BadRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Translation\Translator;

class Locale
{
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
     * @param Translator $translator
     * @param array $availableLocales
     */
    public function __construct(Router $router, Translator $translator, array $availableLocales)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->availableLocales = $availableLocales;
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
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param callable $callback
     * @param string   $locale
     *
     * @return mixed
     */
    public function executeWithUserLocale(Closure $callback, $locale)
    {
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

        if (!in_array($locale, $this->availableLocales)) {
            $availableLocalesTest = implode(', ', $this->availableLocales);

            throw new BadRequest(
                'unavailableLocale',
                "The locale $locale should belong to [$availableLocalesTest]."
            );
        }
    }

    private function assertNotNull($locale)
    {
        if (empty($locale)) {
            throw new BadRequest(
                'noValidLocaleGiven',
                "A valid locale must be given."
            );
        }
    }
}
