<?php
/**
 * @package Joomla.Plugin
 * @subpackage Captcha.Mosparo
 * @copyright (C) 2026 Yann Gomiero
 * @license GNU General Public License version 2 or later
 */
namespace Joomla\Plugin\Captcha\Mosparo\Service;

use Joomla\Registry\Registry;
use Mosparo\ApiClient\Client;

\defined('_JEXEC') or die;

/** Fail-closed validation using the unmodified, pinned official mosparo client. */
final class Verifier
{
    public function __construct(private Registry $params) {}

    /** Only the specified field values and the two mosparo tokens reach the API. */
    public function verify(array $post, array $fields): bool
    {
        try {
            $data = FormData::extract($post, $fields);
            $submit = $post['_mosparo_submitToken'] ?? null;
            $validation = $post['_mosparo_validationToken'] ?? null;
            if (!is_string($submit) || !is_string($validation) || $submit === '' || $validation === ''
                || strlen($submit) > 512 || strlen($validation) > 512) {
                return false;
            }
            $url = Renderer::validatedUrl((string) $this->params->get('mosparo_url'));
            $public = (string) $this->params->get('mosparo_public_key');
            $private = (string) $this->params->get('mosparo_private_key');
            if ($public === '' || $private === '') {
                return false;
            }
            require_once \JPATH_PLUGINS . '/captcha/mosparo/dependencies/autoload.php';
            $certificateBundle = trim((string) $this->params->get('ca_bundle', ''));
            $verify = (bool) $this->params->get('verify_ssl', true);
            if ($verify && $certificateBundle !== '') {
                if (!is_file($certificateBundle) || !is_readable($certificateBundle)) return false;
                $verify = $certificateBundle;
            }
            $client = new Client($url, $public, $private, [
                'verify' => $verify,
                'timeout' => 10,
                'connect_timeout' => 5,
                'allow_redirects' => false,
            ]);
            $result = $client->verifySubmission($data, $submit, $validation);
            if (!$result->isSubmittable()) {
                return false;
            }
            // A valid signed response alone is insufficient: every required field must be verified.
            foreach ($fields as $name) {
                if ($result->getVerifiedField($name) !== 'valid') {
                    return false;
                }
            }
            return true;
        } catch (\Throwable $exception) {
            // Do not leak submitted values, keys, tokens or remote diagnostics to visitors/logs.
            return false;
        }
    }
}
