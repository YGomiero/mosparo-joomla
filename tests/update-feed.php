<?php
/** Copyright 2026 Yann Gomiero. GPL-2.0-or-later. */
require_once dirname(__DIR__) . '/tools/update-feed.php';
$dir = $argv[1] ?? dirname(__DIR__) . '/dist';
$version = (string) simplexml_load_file(dirname(__DIR__) . '/pkg_mosparo.xml')->version;
$archive = $dir . '/pkg_mosparo-' . $version . '.zip';
writeUpdateFeed($archive, $version, $dir);
$xml = simplexml_load_file($dir . '/update.xml')->update;
if ((string) $xml->client !== 'site' || isset($xml->client_id) || (string) $xml->element !== 'pkg_mosparo'
    || (string) $xml->type !== 'package' || (string) $xml->version !== $version
    || (string) $xml->sha256 !== hash_file('sha256', $archive)
    || (string) $xml->downloads->downloadurl !== 'https://github.com/YGomiero/mosparo-joomla/releases/download/' . $version . '/' . basename($archive)) {
    throw new RuntimeException('Feed cannot identify the installed package or its exact release.');
}
try {
    writeUpdateFeed($archive, '9.9.9', $dir);
    throw new LogicException('Version mismatch was accepted.');
} catch (InvalidArgumentException $expected) {}
// In CI, also check against the installed Joomla updater's real accepted XML fields.
if (isset($argv[2])) {
    define('_JEXEC', 1);
    define('JPATH_BASE', realpath($argv[2]));
    require $argv[2] . '/includes/defines.php';
    require $argv[2] . '/libraries/vendor/autoload.php';
    $adapter = new ReflectionClass(\Joomla\CMS\Updater\Adapter\ExtensionAdapter::class);
    $fields = $adapter->getDefaultProperties()['updatecols'];
    if (!in_array('CLIENT', $fields, true)
        || \Joomla\CMS\Application\ApplicationHelper::getClientInfo((string) $xml->client, true)->id !== 0) {
        throw new RuntimeException('Joomla cannot map the feed client to the installed package.');
    }
}
echo "PASS: package identity, client, version, versioned URL, checksum and mismatch rejection.\n";
