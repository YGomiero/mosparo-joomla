<?php
/**
 * @copyright (C) 2026 Yann Gomiero
 * @license GNU General Public License version 2 or later
 */
\defined('_JEXEC') or die;

/** Reject unsupported environments before installing any package children. */
class PkgMosparoInstallerScript
{
    public function preflight($type, $parent): bool
    {
        if ($type === 'uninstall') return true;
        if (version_compare(JVERSION, '6.0', '<') || version_compare(JVERSION, '7.0', '>=') || PHP_VERSION_ID < 80300) {
            \Joomla\CMS\Factory::getApplication()->enqueueMessage('mosparo: Joomla 6 and PHP 8.3+ are required / Joomla 6 et PHP 8.3+ sont requis.', 'error');
            return false;
        }
        return true;
    }
}
