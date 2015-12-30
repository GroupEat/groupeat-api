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

    private $locale;

    public function __construct(Translator $translator, AvailableLocales $availableLocales)
    {
        $this->translator = $translator;
        $this->availableLocales = $availableLocales->value();
    }

    public function get(): string
    {
        return $this->locale;
    }

    public function set(string $locale): Locale
    {
        $this->assertAvailable($locale);

        $this->locale = $locale;

        return $this;
    }

    public function getTranslator()
    {
        return $this->translator;
    }

    public function executeWithUserLocale(Closure $callback, string $locale)
    {
        $this->assertNotNull($locale);

        $previousLocale = $this->translator->getLocale();
        $this->translator->setLocale($locale);

        $res = $callback();

        $this->translator->setLocale($previousLocale);

        return $res;
    }

    public function assertAvailable(string $locale)
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
