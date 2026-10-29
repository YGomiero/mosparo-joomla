<?php
/**
 * @copyright (C) 2026 Yann Gomiero
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */

// Inspect the actual nested distribution ZIPs, not only the source checkout.
$root = dirname(__DIR__);
$out = $argv[1] ?? $root . '/dist';
$version = (string) simplexml_load_file($root . '/pkg_mosparo.xml')->version;
$package = new ZipArchive();
if ($package->open($out . '/pkg_mosparo-' . $version . '.zip') !== true) throw new RuntimeException('Cannot open package');
$count = 1;
$dependencies = 0;
if ($package->getFromName('script.php') !== file_get_contents($root . '/script.php')) throw new RuntimeException('Installer changed');
if ($package->getFromName('LICENSE.txt') !== file_get_contents($root . '/LICENSE.txt')) throw new RuntimeException('GPL text changed');

function executableTokens(string $php): array
{
    $result = [];
    foreach (token_get_all($php, TOKEN_PARSE) as $token) {
        if (is_array($token)) {
            if (in_array($token[0], [T_COMMENT, T_DOC_COMMENT, T_WHITESPACE], true)) continue;
            $result[] = [$token[0], $token[0] === T_OPEN_TAG ? trim($token[1]) : $token[1]];
        } else {
            $result[] = $token;
        }
    }
    return $result;
}

foreach (['captcha/mosparo', 'system/mosparologin'] as $plugin) {
    $name = 'plg_' . str_replace('/', '_', $plugin) . '.zip';
    if ($package->getFromName('packages/' . $name) !== file_get_contents($out . '/' . $name)) throw new RuntimeException('Nested ZIP mismatch');
    $zip = new ZipArchive();
    if ($zip->open($out . '/' . $name) !== true) throw new RuntimeException('Cannot open plugin');
    for ($i = 0; $i < $zip->numFiles; ++$i) {
        $path = $zip->getNameIndex($i);
        $built = $zip->getFromIndex($i);
        $original = file_get_contents($root . '/plugins/' . $plugin . '/' . $path);
        if (!str_ends_with($path, '.php')) {
            if ($built !== $original) throw new RuntimeException('Non-PHP file changed: ' . $path);
            continue;
        }
        ++$count;
        if (str_starts_with($path, 'dependencies/')) {
            ++$dependencies;
            if (!preg_match('~^<\?php\s+/\*\*(.*?)\*/~s', $built, $match)
                || !str_contains($match[1], '@license MIT License (MIT)')
                || !str_contains($match[1], 'Permission is hereby granted, free of charge')
                || !str_contains($match[1], 'Copyright')) {
                throw new RuntimeException('Incomplete MIT header: ' . $path);
            }
            $vendor = $root . '/plugins/' . $plugin . '/dependencies';
            $licenseDir = $path === 'dependencies/autoload.php' ? $vendor . '/composer' : dirname($root . '/plugins/' . $plugin . '/' . $path);
            while (!is_file($licenseDir . '/LICENSE') && str_starts_with($licenseDir, $vendor . '/')) $licenseDir = dirname($licenseDir);
            if (!is_file($licenseDir . '/LICENSE')) throw new RuntimeException('Upstream licence missing: ' . $path);
            $license = trim(str_replace("\r\n", "\n", file_get_contents($licenseDir . '/LICENSE')));
            $notice = preg_replace('/^ \* ?/m', '', $match[1]);
            if (!str_contains($notice, $license)) throw new RuntimeException('Wrong upstream attribution or licence: ' . $path);
            // Existing notices and every original source byte after the opening tag must survive.
            if (substr($built, strlen($match[0]) + 1) !== substr($original, 5)) throw new RuntimeException('Upstream source changed: ' . $path);
            if (executableTokens($built) !== executableTokens($original)) throw new RuntimeException('Executable PHP changed: ' . $path);
        } elseif ($built !== $original || !preg_match('/@license\s+GNU General Public License/', substr($built, 0, 600))) {
            throw new RuntimeException('Integration licence or source changed: ' . $path);
        }
    }
    $zip->close();
}
$package->close();
if ($dependencies === 0) throw new RuntimeException('Dependencies missing from package');
echo "$count PHP files checked; $dependencies upstream MIT notices; executable tokens and original files preserved.\n";
