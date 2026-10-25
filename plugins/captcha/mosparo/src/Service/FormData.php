<?php
/**
 * @package Joomla.Plugin
 * @subpackage Captcha.Mosparo
 * @copyright (C) 2026 Yann Gomiero
 * @license GNU General Public License version 2 or later
 */
namespace Joomla\Plugin\Captcha\Mosparo\Service;

\defined('_JEXEC') or die;

/** Server-owned field profiles. Never trust a field list submitted by a browser. */
final class FormData
{
    public const CONTACT = ['jform[contact_name]', 'jform[contact_email]', 'jform[contact_subject]', 'jform[contact_message]'];

    /** Read only the required top-level values, without Joomla's text filters. */
    public static function readPost(\Joomla\Input\Input $post, array $fields): array
    {
        $data = [];
        foreach (array_merge($fields, ['_mosparo_submitToken', '_mosparo_validationToken']) as $field) {
            $name = explode('[', $field, 2)[0];
            $data[$name] = $post->get($name, null, 'RAW');
        }
        return $data;
    }

    /** Select a profile from the Joomla route, including core account forms. */
    public static function fields(string $option, string $view, string $task, string $custom = ''): array
    {
        if ($option === 'com_contact') {
            return self::CONTACT;
        }
        if ($option === 'com_users') {
            if ($view === 'registration' || $task === 'registration.register') {
                return ['jform[name]', 'jform[username]', 'jform[email1]'];
            }
            if (in_array($view, ['remind', 'reset'], true) || in_array($task, ['remind.remind', 'reset.request'], true)) {
                return ['jform[email]'];
            }
        }
        return array_values(array_unique(array_filter(array_map('trim', preg_split('/[\r\n]+/', $custom)))));
    }

    /** Resolve HTML bracket names without filtering or altering the signed text. */
    public static function extract(array $post, array $fields): array
    {
        if (!$fields) {
            throw new \InvalidArgumentException('No server-side field profile.');
        }
        $result = [];
        foreach ($fields as $name) {
            if (!is_string($name) || !preg_match('/^[a-zA-Z_][a-zA-Z0-9_-]*(?:\[[a-zA-Z0-9_-]+\])*$/D', $name)
                || preg_match('/password|passwd|secret|token/i', $name)) {
                throw new \InvalidArgumentException('Invalid or sensitive field name.');
            }
            preg_match_all('/[^\[\]]+/', $name, $keys);
            $value = $post;
            foreach ($keys[0] as $key) {
                if (!is_array($value) || !array_key_exists($key, $value)) {
                    throw new \InvalidArgumentException('A protected field is missing.');
                }
                $value = $value[$key];
            }
            if (!is_string($value) && !is_numeric($value)) {
                throw new \InvalidArgumentException('Unexpected field value.');
            }
            $result[$name] = (string) $value;
        }
        return $result;
    }
}
