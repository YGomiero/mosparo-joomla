<?php
/**
 * @copyright (C) 2026 Yann Gomiero
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */

/** Build notices from the locked upstream licences, without changing vendor files. */
function dependencyLicenseNotices(string $root): array
{
    $vendor = $root . '/plugins/captcha/mosparo/dependencies';
    $lock = json_decode(file_get_contents($root . '/composer.lock'), true, 512, JSON_THROW_ON_ERROR);
    $notices = [];
    foreach ($lock['packages'] as $package) {
        if ($package['license'] !== ['MIT']) {
            throw new RuntimeException('Review the upstream licence before packaging: ' . $package['name']);
        }
        $notices[$package['name'] . '/'] = mitNotice($vendor . '/' . $package['name'] . '/LICENSE', $package['name']);
    }
    // Composer runtime classes and generated autoload/metadata templates retain Composer's MIT notice.
    $composer = mitNotice($vendor . '/composer/LICENSE', 'Composer');
    $notices['composer/'] = $composer;
    $notices['autoload.php'] = $composer;
    return $notices;
}

function mitNotice(string $licenseFile, string $package): string
{
    $license = is_file($licenseFile) ? trim(str_replace("\r\n", "\n", file_get_contents($licenseFile))) : '';
    if (!str_contains($license, 'Permission is hereby granted, free of charge') || str_contains($license, '*/')) {
        throw new RuntimeException('Missing or unexpected MIT licence: ' . $package);
    }
    // Reproduce the complete upstream text, including its original copyright attribution.
    $lines = "Upstream licence notice for " . $package . ".\n"
        . "@license MIT License (MIT)\nSPDX-License-Identifier: MIT\n\n" . $license;
    return "/**\n * " . str_replace("\n", "\n * ", $lines) . "\n */\n";
}

function dependencyPhpWithNotice(string $name, string $content, array $notices): string
{
    $relative = substr($name, strlen('dependencies/'));
    foreach ($notices as $prefix => $notice) {
        if ($relative === $prefix || (str_ends_with($prefix, '/') && str_starts_with($relative, $prefix))) {
            if (!preg_match('/^<\?php\s/', $content)) {
                throw new RuntimeException('Unexpected PHP opening tag: ' . $name);
            }
            return "<?php\n" . $notice . substr($content, 5);
        }
    }
    throw new RuntimeException('No reviewed upstream licence for ' . $name);
}
