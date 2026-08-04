<?php

declare(strict_types=1);

$files = [
    'resources/views/home.blade.php',
    'resources/views/filament/pages/auth/register.blade.php',
];

$legalLabels = [
    "{{ __('Terms of Service') }}",
    "{{ __('Privacy Policy') }}",
    "{{ __('Cookie Policy') }}",
];

$canonicalUrls = [
    'https://cegem360.eu/szolgaltatasi-feltetelek',
    'https://cegem360.eu/adatvedelmi-tajekoztato',
    'https://cegem360.eu/cookie-beallitasok',
];

test('a jogi linkek a cegem360.eu kozponti oldalaira mutatnak', function () use ($files): void {
    foreach ($files as $file) {
        $path = base_path($file);

        expect(file_exists($path))->toBeTrue();

        $contents = file_get_contents($path);

        expect($contents)->toContain('https://cegem360.eu/szolgaltatasi-feltetelek');
        expect($contents)->toContain('https://cegem360.eu/adatvedelmi-tajekoztato');
    }
});

test('minden jogi link a harom kanonikus URL egyikere mutat', function () use ($files, $legalLabels, $canonicalUrls): void {
    $offenders = [];

    foreach ($files as $file) {
        foreach (file(base_path($file), FILE_IGNORE_NEW_LINES) as $number => $line) {
            foreach ($legalLabels as $label) {
                if (! str_contains($line, $label)) {
                    continue;
                }

                if (preg_match('/<a\s+href="([^"]*)"/', $line, $matches) !== 1
                    || ! in_array($matches[1], $canonicalUrls, true)) {
                    $offenders[] = $file.':'.($number + 1);
                }
            }
        }
    }

    expect($offenders)->toBe([]);
});
