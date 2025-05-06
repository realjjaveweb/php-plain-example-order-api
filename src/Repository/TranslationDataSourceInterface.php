<?php

declare(strict_types=1);

namespace App\Repository;

interface TranslationDataSourceInterface
{
    public function getTranslation(string $key, string $langCode): ?string;

    /**
     * @param list<string> $keys
     * @return array<string,string|null> - key => translation/null
     */
    public function getTranslations(array $keys, string $langCode): array;
}
