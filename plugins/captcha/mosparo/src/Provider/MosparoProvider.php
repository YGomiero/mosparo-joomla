<?php
/**
 * @package Joomla.Plugin
 * @subpackage Captcha.Mosparo
 * @copyright (C) 2026 Yann Gomiero
 * @license GNU General Public License version 2 or later
 */
namespace Joomla\Plugin\Captcha\Mosparo\Provider;

use Joomla\CMS\Application\CMSWebApplicationInterface;
use Joomla\CMS\Captcha\CaptchaProviderInterface;
use Joomla\CMS\Form\FormField;
use Joomla\Plugin\Captcha\Mosparo\Service\FormData;
use Joomla\Plugin\Captcha\Mosparo\Service\Renderer;
use Joomla\Plugin\Captcha\Mosparo\Service\Verifier;
use Joomla\Registry\Registry;

\defined('_JEXEC') or die;

/** Joomla 6 CAPTCHA provider for core contact and account forms. */
final class MosparoProvider implements CaptchaProviderInterface
{
    public function __construct(private Registry $params, private CMSWebApplicationInterface $application) {}

    public function getName(): string { return 'mosparo'; }

    public function setupField(FormField $field, \SimpleXMLElement $element): void
    {
        $element['hiddenLabel'] = 'true';
    }

    private function fields(): array
    {
        $input = $this->application->getInput();
        return FormData::fields($input->getCmd('option', ''), $input->getCmd('view', ''), $input->getCmd('task', ''), (string) $this->params->get('required_fields', implode("\n", FormData::CONTACT)));
    }

    public function display(string $name = '', array $attributes = []): string
    {
        return (new Renderer($this->params, $this->application))->display($this->fields(), $attributes['id'] ?? '');
    }

    public function checkAnswer(?string $code = null): bool
    {
        $fields = $this->fields();
        return (new Verifier($this->params))->verify(FormData::readPost($this->application->getInput()->post, $fields), $fields);
    }
}
