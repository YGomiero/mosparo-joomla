<?php
/**
 * @package Joomla.Plugin
 * @subpackage System.Mosparologin
 * @copyright (C) 2026 Yann Gomiero
 * @license GNU General Public License version 2 or later
 */
namespace Yann\Plugin\System\Mosparologin\Extension;

use Joomla\CMS\Event\Application\AfterRouteEvent;
use Joomla\CMS\Event\Application\BeforeCompileHeadEvent;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\SubscriberInterface;
use Joomla\Plugin\Captcha\Mosparo\Service\Renderer;
use Joomla\Plugin\Captcha\Mosparo\Service\FormData;
use Joomla\Plugin\Captcha\Mosparo\Service\Verifier;
use Joomla\Registry\Registry;

\defined('_JEXEC') or die;

/** Adds mosparo to native password logins and validates before authentication. */
final class Mosparologin extends CMSPlugin implements SubscriberInterface
{
    protected $autoloadLanguage = true;

    public static function getSubscribedEvents(): array
    {
        return ['onAfterRoute' => 'guardLogin', 'onBeforeCompileHead' => 'addAssets'];
    }

    /** Site/admin protections are independently opt-in to prevent accidental lockout. */
    private function enabledForClient(): bool
    {
        $app = $this->getApplication();
        return ($app->isClient('site') && (bool) $this->params->get('protect_site', false))
            || ($app->isClient('administrator') && (bool) $this->params->get('protect_admin', false));
    }

    private function captchaParams(): Registry
    {
        if (!PluginHelper::isEnabled('captcha', 'mosparo')) {
            throw new \RuntimeException('The mosparo CAPTCHA plugin must be enabled.');
        }
        // Register the captcha extension namespace before using its shared services.
        $this->getApplication()->bootPlugin('mosparo', 'captcha');
        return new Registry(PluginHelper::getPlugin('captcha', 'mosparo')->params);
    }

    /** Route controls are checked server-side; hiding/removing JS cannot bypass this gate. */
    public function guardLogin(AfterRouteEvent $event): void
    {
        if (!$this->enabledForClient()) {
            return;
        }
        $app = $this->getApplication();
        $input = $app->getInput();
        $option = strtolower($input->getCmd('option', ''));
        $task = strtolower($input->getCmd('task', ''));
        $isLogin = ($app->isClient('site') && $option === 'com_users' && $task === 'user.login')
            || ($app->isClient('administrator') && $option === 'com_login' && in_array($task, ['login', 'login.login'], true));
        if (!$isLogin) {
            return;
        }
        $valid = false;
        try {
            $valid = $input->getMethod() === 'POST'
                && (new Verifier($this->captchaParams()))->verify(FormData::readPost($input->post, ['username']), ['username']);
        } catch (\Throwable $exception) {
            $valid = false;
        }
        if (!$valid) {
            throw new \RuntimeException(Text::_('PLG_SYSTEM_MOSPAROLOGIN_REJECTED'), 403);
        }
    }

    /** The shared JS discovers core login forms, including multiple mod_login instances. */
    public function addAssets(BeforeCompileHeadEvent $event): void
    {
        if (!$this->enabledForClient() || !$this->getApplication()->getIdentity()->guest) {
            return;
        }
        try {
            (new Renderer($this->captchaParams(), $this->getApplication()))->assets(true);
        } catch (\Throwable $exception) {
            $this->getApplication()->enqueueMessage(Text::_('PLG_SYSTEM_MOSPAROLOGIN_CONFIGURATION_ERROR'), 'error');
        }
    }
}
