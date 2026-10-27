<?php
/**
 * @package Joomla.Plugin
 * @subpackage System.Mosparologin
 * @copyright (C) 2026 Yann Gomiero
 * @license GNU General Public License version 2 or later
 */
namespace Yann\Plugin\System\Mosparologin\Service;

use Joomla\CMS\Uri\Uri;

\defined('_JEXEC') or die;

/** Build a fresh login GET without storing or forwarding submitted credentials or mosparo tokens. */
final class LoginRecovery
{
    public static function url(bool $administrator, string $return): string
    {
        $url = Uri::base() . 'index.php' . ($administrator ? '' : '?option=com_users&view=login');
        $decoded = base64_decode($return, true);
        // Keep Joomla's local destination (including a pending external-account confirmation).
        if (is_string($decoded) && $decoded !== '' && strlen($decoded) <= 2048
            && !preg_match('/[\x00-\x20\x7f\\\\]/', $decoded) && Uri::isInternal($decoded)) {
            $url .= ($administrator ? '?' : '&') . 'return=' . rawurlencode(base64_encode($decoded));
        }
        return $url;
    }
}
