<?php
/** Reproducible installable package builder. Copyright 2026 Yann Gomiero. GPL-2.0-or-later. */
$root = dirname(__DIR__);
$version = (string) simplexml_load_file($root . '/pkg_mosparo.xml')->version;
$out = $argv[1] ?? $root . '/dist';
if (!is_dir($out)) mkdir($out, 0777, true);
function zipTree(string $source, string $target): void {
    $zip = new ZipArchive();
    if ($zip->open($target, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) throw new RuntimeException('Cannot open ZIP');
    $files = [];
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS)) as $f) {
        if ($f->isFile()) $files[] = $f->getPathname();
    }
    sort($files);
    foreach ($files as $path) {
        $name = str_replace('\\', '/', substr($path, strlen($source) + 1));
        // Keep runtime code and original licences; omit upstream development-only assets.
        if (str_starts_with($name, 'dependencies/') && preg_match('~/(?:tests?|docs?|\\.github)/|/(?:\\.gitignore|\\.gitattributes|phpunit\\.xml(?:\\.dist)?|phpstan\\.neon(?:\\.dist)?)$~i', $name)) continue;
        $zip->addFile($path, $name);
        $zip->setMtimeName($name, 1792886400);
    }
    $zip->close();
}
if (!is_file($root . '/plugins/captcha/mosparo/dependencies/autoload.php')) throw new RuntimeException('Run composer install first.');
foreach (['captcha/mosparo', 'system/mosparologin'] as $plugin) {
    $manifest = simplexml_load_file($root . '/plugins/' . $plugin . '/' . basename($plugin) . '.xml');
    if ((string) $manifest->version !== $version) throw new RuntimeException('Package and plugin versions differ.');
    zipTree($root . '/plugins/' . $plugin, $out . '/plg_' . str_replace('/', '_', $plugin) . '.zip');
}
$name = 'pkg_mosparo-' . $version . '.zip';
$zip = new ZipArchive();
$zip->open($out . '/' . $name, ZipArchive::CREATE | ZipArchive::OVERWRITE);
foreach (['pkg_mosparo.xml','script.php','LICENSE.txt'] as $file) $zip->addFile($root.'/'.$file, $file);
foreach (glob($root.'/language/*/*.ini') as $file) $zip->addFile($file, str_replace('\\','/',substr($file,strlen($root)+1)));
foreach (['plg_captcha_mosparo.zip','plg_system_mosparologin.zip'] as $file) $zip->addFile($out.'/'.$file, 'packages/'.$file);
for ($i = 0; $i < $zip->numFiles; $i++) $zip->setMtimeIndex($i,1792886400);
$zip->close();
require_once __DIR__ . '/update-feed.php';
writeUpdateFeed($out.'/'.$name, $version, $out);
echo $out.'/'.$name.PHP_EOL.hash_file('sha256', $out.'/'.$name).PHP_EOL;
