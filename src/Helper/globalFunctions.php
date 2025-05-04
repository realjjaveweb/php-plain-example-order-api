<?php

declare(strict_types=1);

//
// INTENTIONALLY NOT NAMESPACED
//

define('APP_ROOT', \realpath(__DIR__ . '/../..'));

if (!function_exists('isCli')) {
    function isCli(): bool
    {
        return \PHP_SAPI === 'cli' || \PHP_SAPI === 'phpdbg';
    }
}

if (!function_exists('d')) {
    function d(mixed ...$data): void
    {
        if (isCli()) {
            $cliLineLength = 80;
            $cliLine = "\033[1;32m" . \str_repeat('-', $cliLineLength)."\033[0m" . \PHP_EOL;
            echo $cliLine;
        } else {
            $preCss = 'background-color: #000; color: #fff; font-size: 14px; padding: 10px; border-radius: 5px; border: 1px solid #0f0; margin: 10px 0;';
        }

        foreach ($data as $key => $item) {
            echo isCli() ? '' : '<pre style="'.$preCss.'">';
            echo isCli() ? "\033[1;32m" : '<span style="font-weight: bold; color: #0f0;">';
            echo '#[' . $key . ']:';
            echo isCli() ? "\033[0m" : '</span>';
            echo \PHP_EOL;
            \var_dump($item);
            echo \PHP_EOL;
            echo isCli() ? $cliLine : '</pre>';
        }
    }
}

if (!function_exists('dd')) {
    function dd(mixed ...$data): void
    {
        d(...$data);
        exit(1); // intentionally exit with an error code instead of success (0)
    }
}
