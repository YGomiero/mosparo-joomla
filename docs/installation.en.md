# Installation and configuration

[Français](installation.fr.md) · [Home](../README.md)

## Prepare mosparo

Provide a working mosparo instance reachable over HTTPS from both visitors' browsers and Joomla's PHP server. The package does not install mosparo itself. Create a project, add permitted hostnames without paths, and copy its UUID, public key and private key into Joomla. Never expose the private key in screenshots, repositories or support tickets.

PHP needs cURL, JSON and Joomla's required extensions. Dependencies are bundled: Composer is not needed on the destination website.

## Install

Back up files and database. Install `pkg_mosparo-1.1.0.zip` using Joomla's installer. This package contains two plugins and one update source. Do not install its internal ZIPs separately. Uninstall the package to remove both plugins; the remote mosparo project remains intact.

The `captcha/mosparo` element and namespace are retained for upgrading an existing 1.0.2 plugin. Check configuration after installation. The original development source website was not changed.

## CAPTCHA settings

| Setting | Purpose |
|---|---|
| URL | mosparo root, optionally in a subdirectory; no administrator path or query string |
| UUID / public key | Identify the project and are exposed to the widget |
| Private key | Used only by the server; stored in Joomla plugin parameters |
| Load CSS | Keep enabled to use the project's mosparo appearance |
| Verify SSL | Keep enabled in production |
| CA bundle | Optional absolute path to a trusted CA certificate/bundle, useful locally |
| Required fields | Fallback for custom integrations: exact HTML field names, one per line |

Native profiles are selected by the server. The contact profile uses `jform[contact_name]`, `jform[contact_email]`, `jform[contact_subject]`, `jform[contact_message]`. Field names containing `password`, `passwd`, `secret` or `token` are rejected. Missing fields or unexpected structures fail verification. Joomla still performs its own input and session checks.

## Contact and site login

Select **mosparo** as CAPTCHA in Contacts options, checking individual contact overrides. Publish a contact/menu item. Test ordinary content and a temporary demonstration spam rule.

Enable the System plugin and **Protect site logins**. It inserts a widget into the core Users login page and Login modules, including multiple forms on the same page. No Joomla core edits or template overrides are required.

## Administrator login and recovery

Test the frontend first. Keep an administrator session open, enable **Protect administrator login**, then test another session. Retain filesystem/database access: protected logins fail when mosparo is unavailable.

A server administrator can recover access by disabling only the login plugin in the database. Replace `PREFIX_` with the real prefix:

```sql
UPDATE PREFIX_extensions SET enabled = 0
WHERE type = 'plugin' AND folder = 'system' AND element = 'mosparologin';
```

Repair configuration/certificates, re-enable and retest. Password checks, MFA, session protection and login rate limiting remain necessary.

## Troubleshooting

- **Missing widget:** check plugin status, protection switches, blocked scripts and browser errors. Third-party overrides may need adaptation.
- **Rejection after a green checkbox:** check keys, PHP trust store, outgoing connectivity and verified fields; inspect verification details in mosparo.
- **HTTP 403:** return to the form and reload; missing or invalid tokens cannot establish validation.
- **Legitimate content blocked:** identify the matching rule and score in mosparo, then refine it.
- **Local HTTPS:** trust the local CA in the browser and PHP. Use the CA option while retaining TLS verification.

Allow the mosparo origin in the website's applicable CSP resource and connection policies. This extension does not rewrite CSP.

## Data and updates

The browser sends protected fields and mosparo's technical information to your mosparo server. PHP verifies hashes/signatures using the official client. Explain the actual processing, hosting and retention to visitors. For login forms, only `username` is analysed; this integration never sends the password to mosparo.

Use **System → Update → Extensions**. Keep the package update site enabled and ensure PHP can reach GitHub. Back up before upgrading. Repository documentation can change: consult your version's tag for its exact documentation.
