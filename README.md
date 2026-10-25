# mosparo for Joomla

[![Release](https://img.shields.io/github/v/release/YGomiero/mosparo-joomla?color=green&label=release&style=for-the-badge)](https://github.com/YGomiero/mosparo-joomla/releases/latest)
[![Joomla 6](https://img.shields.io/badge/Joomla-6.x-1769aa?style=for-the-badge)](docs/installation.en.md)
[![PHP 8.3+](https://img.shields.io/badge/PHP-8.3%2B-777bb4?style=for-the-badge)](docs/installation.en.md)
[![GPL 2.0 or later](https://img.shields.io/badge/license-GPL--2.0--or--later-blue?style=for-the-badge)](LICENSE.txt)

[English](README.md) · [Français](README.fr.md) · [Download](https://github.com/YGomiero/mosparo-joomla/releases/latest) · [Support](https://github.com/YGomiero/mosparo-joomla/issues)

![mosparo for Joomla: rules and statistics](docs/images/banner-jed-1200x525.png)

**Custom rules, statistics to refine them, and protection for Joomla forms.**

> **Required: mosparo must already be installed, configured and reachable.** This package integrates Joomla with mosparo; it does not install the mosparo server. Provide a self-hosted instance or hosted mosparo service and a project with its keys. [Install mosparo](https://documentation.mosparo.io/docs/category/installation).

This package connects Joomla to your [mosparo](https://mosparo.io) instance. It includes **CAPTCHA – mosparo** for contact forms and **System – mosparo: logins** for the core login page, Login modules and administrator password login. Manage rules, submissions and statistics in mosparo.

Version **1.1.0** · Joomla **6.x**, PHP **8.3+** · Tested with Joomla **6.2.0**, PHP **8.3.30**, mosparo **1.5.12** · Official PHP client **2.0.1**.

## Install

1. Download **pkg_mosparo-1.1.0.zip** from the release. GitHub's “Source code” archive cannot be installed in Joomla.
2. Upload the package through **System → Install → Extensions**.
3. Configure and enable **CAPTCHA – mosparo** with the project URL, UUID and both keys. Add the website domain to the project's allowed hosts.
4. Select mosparo in the Contacts component CAPTCHA setting and test a contact submission.
5. Enable **System – mosparo: logins**, then its site protection. Enable administrator protection after a successful test, keeping an administrator session open.

Login protection is off by default. Upgrading preserves existing CAPTCHA settings. Back up the site first.

## Documentation

- [Installation, settings and troubleshooting](docs/installation.en.md)
- [mosparo: rules, statistics and Joomla comparison](docs/mosparo.en.md)
- [Architecture and development](docs/development.md)
- [Changelog](CHANGELOG.md), [security](SECURITY.md), [credits and licences](THIRD-PARTY.md)

## Updates

Joomla updates the **complete package** through its declared update server. Each release contains the ZIP, `update.xml` and `SHA256SUMS`. The [stable update feed](https://github.com/YGomiero/mosparo-joomla/releases/latest/download/update.xml) points to a versioned archive. Update the mosparo server separately.

Stable version tags trigger a GitHub Action that tests, builds and publishes the package and update metadata. A separate workflow generates metadata for manually published stable releases. See the [release procedure](docs/development.md#release). Once a JED listing is approved, its information must still be maintained in the directory: Joomla's update feed does not automatically edit the JED listing.

## Scope

Server verification runs before authentication on native password login routes. Only the username goes to mosparo for those forms; passwords stay in Joomla, including when “Show password” is used. Passkeys, remembered sessions, API authentication and third-party login endpoints are outside this integration.

The standard contact profile covers name, email, subject and message. Registration, username reminder and password-reset request profiles are also included but require acceptance testing on your configuration. Custom fields and third-party forms need their own integration and tests. Statistics remain in mosparo; this package does not add a Joomla statistics dashboard.

![Rules and statistics illustration](docs/images/mosparo-joomla.png)

Independent integration maintained by Yann Gomiero, without endorsement from the mosparo or Joomla teams. Integration code: GPL-2.0-or-later. Bundled libraries and the mosparo logo retain their own credits and licences.
