<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Captcha.Mosparo
 *
 * @copyright   (C) 2026 Yann Gomiero
 * @license     GNU General Public License version 2 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Plugin\Captcha\Mosparo\Extension\Mosparo;

return new class () implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        $container->set(
            PluginInterface::class,
            $container->lazy(Mosparo::class, function (Container $container) {
                $plugin = new Mosparo(
                    (array) PluginHelper::getPlugin('captcha', 'mosparo')
                );
                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            })
        );
    }
};
