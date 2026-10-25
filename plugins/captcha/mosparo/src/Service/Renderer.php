<?php
/**
 * @package Joomla.Plugin
 * @subpackage Captcha.Mosparo
 * @copyright (C) 2026 Yann Gomiero
 * @license GNU General Public License version 2 or later
 */
namespace Joomla\Plugin\Captcha\Mosparo\Service;

use Joomla\CMS\Application\CMSWebApplicationInterface;
use Joomla\Registry\Registry;

\defined('_JEXEC') or die;

/** Shares one frontend loader between contact and login widgets on the same page. */
final class Renderer
{
    public function __construct(private Registry $params, private CMSWebApplicationInterface $app) {}

    /** Disallow URL credentials, queries, fragments and executable schemes. */
    public static function validatedUrl(string $url): string
    {
        $url = rtrim(trim($url), '/');
        $parts = parse_url($url);
        if (!$parts || !in_array($parts['scheme'] ?? '', ['https', 'http'], true)
            || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])
            || isset($parts['query']) || isset($parts['fragment'])) {
            throw new \InvalidArgumentException('Invalid mosparo URL.');
        }
        return $url;
    }

    /** Public configuration only: the private key is never serialised into HTML. */
    public function assets(bool $login = false): void
    {
        $url = self::validatedUrl((string) $this->params->get('mosparo_url'));
        $uuid = (string) $this->params->get('mosparo_uuid');
        $public = (string) $this->params->get('mosparo_public_key');
        if ($uuid === '' || $public === '') {
            throw new \RuntimeException('mosparo is not configured.');
        }
        $document = $this->app->getDocument();
        $wa = $document->getWebAssetManager();
        if (!$wa->assetExists('script', 'plg_captcha_mosparo.remote')) {
            $wa->registerScript('plg_captcha_mosparo.remote', $url . '/build/mosparo-frontend.js', [], ['defer' => true]);
            $wa->registerScript('plg_captcha_mosparo.integration', 'plg_captcha_mosparo/integration.js', ['version' => '1.1.0'], ['defer' => true], ['core', 'plg_captcha_mosparo.remote']);
            $document->addScriptOptions('plg_captcha_mosparo', [
                'url' => $url, 'uuid' => $uuid, 'publicKey' => $public,
                'language' => str_replace('-', '_', $this->app->getLanguage()->getTag()),
                'loadCssResource' => (bool) $this->params->get('load_css_resource', true),
            ]);
        }
        if ($login) {
            $document->addScriptOptions('plg_captcha_mosparo.login', true);
        }
        $wa->useScript('plg_captcha_mosparo.integration');
    }

    /** Render one accessible placeholder; JS adds the native mosparo control. */
    public function display(array $fields, string $id = ''): string
    {
        $this->assets();
        $id = $id !== '' ? $id : 'mosparo-' . bin2hex(random_bytes(6));
        $escape = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        return '<div id="' . $escape($id) . '" class="joomla-mosparo" data-mosparo-fields="'
            . $escape(json_encode($fields, JSON_THROW_ON_ERROR)) . '"></div>';
    }
}
