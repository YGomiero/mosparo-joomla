<?php
/** Copyright 2026 Yann Gomiero. GPL-2.0-or-later. */
require_once dirname(__DIR__) . '/tools/update-feed.php';
$dir = $argv[1] ?? dirname(__DIR__) . '/dist';
$version = (string) simplexml_load_file(dirname(__DIR__) . '/pkg_mosparo.xml')->version;
$archive = $dir . '/pkg_mosparo-' . $version . '.zip';
writeUpdateFeed($archive, $version, $dir);
$xml = simplexml_load_file($dir . '/update.xml')->update;
if ((string) $xml->client_id !== '0' || (string) $xml->element !== 'pkg_mosparo'
    || (string) $xml->type !== 'package' || (string) $xml->version !== $version
    || (string) $xml->sha256 !== hash_file('sha256', $archive)
    || (string) $xml->downloads->downloadurl !== 'https://github.com/YGomiero/mosparo-joomla/releases/download/' . $version . '/' . basename($archive)) {
    throw new RuntimeException('Feed cannot identify the installed package or its exact release.');
}
try {
    writeUpdateFeed($archive, '9.9.9', $dir);
    throw new LogicException('Version mismatch was accepted.');
} catch (InvalidArgumentException $expected) {}
echo "PASS: package identity, client, version, versioned URL, checksum and mismatch rejection.\n";
