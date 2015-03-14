<?php
namespace Groupeat\Support\Services;

use Closure;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Values\AvailableLocales;
use Illuminate\Translation\Translator;

class Locale
{
    private $translator;
    private $availableLocales;

    /**
     * @var string
     */
    private $locale;

    public function __construct(Translator $translator, AvailableLocales $availableLocales)
    {
        $this->translator = $translator;
        $this->availableLocales = $availableLocales->value();
    }

    /**
     * @return string
     */
    public function get()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
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
     * @param string $locale
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
