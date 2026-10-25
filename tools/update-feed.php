<?php
/**
 * Generate Joomla metadata from the actual release ZIP, never from an assumed hash.
 * Copyright 2026 Yann Gomiero. GPL-2.0-or-later.
 */
function writeUpdateFeed(string $archive, string $version, string $output): void
{
    if (!preg_match('/^[0-9]+\.[0-9]+\.[0-9]+$/D', $version)) {
        throw new InvalidArgumentException('Use a stable numeric tag, for example 1.1.0.');
    }
    $name = 'pkg_mosparo-' . $version . '.zip';
    if (basename($archive) !== $name) throw new InvalidArgumentException('Unexpected package filename.');
    $zip = new ZipArchive();
    if ($zip->open($archive) !== true) throw new RuntimeException('Cannot read release ZIP.');
    $source = $zip->getFromName('pkg_mosparo.xml');
    $zip->close();
    // Reject external entity declarations; the package manifest is data, not executable input.
    if ($source === false || preg_match('/<!DOCTYPE|<!ENTITY/i', $source)) throw new RuntimeException('Invalid manifest.');
    $manifest = simplexml_load_string($source, SimpleXMLElement::class, LIBXML_NONET);
    if (!$manifest || (string) $manifest['type'] !== 'package'
        || (string) $manifest->packagename !== 'mosparo' || (string) $manifest->version !== $version) {
        throw new RuntimeException('Release tag and package manifest must match.');
    }
    $hash = hash_file('sha256', $archive);
    $repo = 'https://github.com/YGomiero/mosparo-joomla';
    $url = $repo . '/releases/download/' . $version . '/' . $name;
    // Packages use client_id 0 in the database; the updater XML uses <client>site</client>.
    // <client_id> is ignored by current Joomla and would leave the administrator default.
    $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n"
        . '<updates><update><name>mosparo for Joomla</name><description>mosparo spam protection for Joomla 6</description>'
        . '<element>pkg_mosparo</element><type>package</type><client>site</client><version>' . $version . '</version>'
        . '<infourl title="mosparo for Joomla">' . $repo . '</infourl>'
        . '<downloads><downloadurl type="full" format="zip">' . $url . '</downloadurl></downloads>'
        . '<tags><tag>stable</tag></tags><maintainer>Yann Gomiero</maintainer><maintainerurl>' . $repo . '</maintainerurl>'
        . '<targetplatform name="joomla" version="6\.[0-9]+"/><php_minimum>8.3</php_minimum><sha256>' . $hash . '</sha256>'
        . '</update></updates>' . "\n";
    if (!is_dir($output) && !mkdir($output, 0777, true)) throw new RuntimeException('Cannot create output folder.');
    if (file_put_contents($output . '/update.xml', $xml) === false
        || file_put_contents($output . '/SHA256SUMS', $hash . '  ' . $name . "\n") === false) {
        throw new RuntimeException('Cannot write update metadata.');
    }
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    if ($argc !== 4) throw new InvalidArgumentException('Usage: php tools/update-feed.php package.zip version output-folder');
    writeUpdateFeed($argv[1], $argv[2], $argv[3]);
    echo "Update feed and SHA256SUMS verified and generated.\n";
}
