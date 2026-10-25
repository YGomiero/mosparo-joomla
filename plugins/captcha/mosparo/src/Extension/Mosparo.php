<?php
/**
 * @package Joomla.Plugin
 * @subpackage Captcha.Mosparo
 * @copyright (C) 2026 Yann Gomiero
 * @license GNU General Public License version 2 or later
 */
namespace Joomla\Plugin\Captcha\Mosparo\Extension;

use Joomla\CMS\Event\Captcha\CaptchaSetupEvent;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\Plugin\Captcha\Mosparo\Provider\MosparoProvider;

\defined('_JEXEC') or die;

/** Registers the provider through Joomla's modern CAPTCHA registry. */
final class Mosparo extends CMSPlugin implements SubscriberInterface
{
    protected $autoloadLanguage = true;

    public static function getSubscribedEvents(): array
    {
        return ['onCaptchaSetup' => 'setupCaptcha'];
    }

    public function setupCaptcha(CaptchaSetupEvent $event): void
    {
        $event->getCaptchaRegistry()->add(new MosparoProvider($this->params, $this->getApplication()));
    }
}
