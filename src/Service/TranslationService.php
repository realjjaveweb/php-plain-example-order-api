<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\TranslationDataSourceInterface;

class TranslationService
{
    // some translation data source, like a no-sql database/cache
    private readonly TranslationDataSourceInterface $translationDataSource;

    public function __construct()
    {
        // fake it for now...
        $this->translationDataSource = new class () implements TranslationDataSourceInterface {
            public function getTranslation(string $key, string $langCode): ?string
            {
                return null;
            }

            public function getTranslations(array $keys, string $langCode): array
            {
                return array_fill_keys($keys, null);
            }
        };
    }

    /**
     * Returns the translation for the $key or the $key itself if translation not found
     */
    public function translate(string $key, ?string $langCode = null): string
    {
        $langCode ??= $this->getCurrentLangCode();

        return $this->translationDataSource->getTranslation($key, $langCode) ?? $key;
    }

    public function getCurrentLangCode(): string
    {
        // TODO
        // imagine a call to some localization service
        return 'en'; // we could also be more specific and use stuff like 'en_US' or 'en_GB'
    }
}
