# Why mosparo? Custom rules and statistics

[Français](mosparo.fr.md) · [Home](../README.md)

## The project

[mosparo](https://mosparo.io) is open-source spam protection backed by a Swiss association. Run your own instance or choose a compatible hosted service. Joomla uses that server to analyse form data and verify submissions. mosparo itself uses the MIT licence; this Joomla integration is independent.

## Compared with Joomla's bundled CAPTCHA

Joomla 6.1 introduced a new native CAPTCHA based on ALTCHA proof of work, also present in Joomla 6.2. Describing today's bundled Joomla CAPTCHA as mandatory Google tracking would be inaccurate. [Official Joomla introduction](https://magazine.joomla.org/issues/2026/january-2026/built-in-captcha-returning-to-joomla-core).

| Requirement | Joomla 6.2 native CAPTCHA | mosparo with this package |
|---|---|---|
| Discourage automated submissions | Browser proof of work | Content analysis and configured mosparo protections |
| Adapt to recurring spam | No comparable content-rule manager in the plugin | Rules tailored to observed messages |
| Understand rejections | No mosparo submission dashboard | Submission details, matched rules and statistics in mosparo |
| Hosting | Built into Joomla | Additional service to maintain |
| Native logins | A global CAPTCHA setting alone does not protect every login | Dedicated plugin with separate site/admin switches |

The native option suits simple needs with less infrastructure. Choose mosparo when you want to inspect spam, refine rules and observe the results. Poor rules can reject legitimate visitors; statistics still need human interpretation.

## Useful starting rules

mosparo assigns points to matches and compares the score with a project threshold. Types include text, email, domains, URLs and technical data. A substring match is broader than a whole-word match. See [rule types](https://documentation.mosparo.io/docs/usage/rule_types) and [rules](https://documentation.mosparo.io/docs/usage/rules).

These are **proposed experiments**, not universal settings. Example domains are not real blocklists.

| Observed pattern | Suggested experiment | Acceptance check |
|---|---|---|
| A campaign repeats a distinctive phrase | A narrow text rule with a date/reason in its name | Legitimate messages using related words |
| Advertisements repeat a website address | A targeted domain/URL rule | Legitimate users quoting that address |
| One known mailbox repeatedly abuses the form | Exact-address rule | Review periodically; avoid blocking an entire provider |
| Spam follows a stable structure | A small documented regular expression | Accents, apostrophes, line breaks and long messages |
| Automated submissions arrive unusually fast | Adjust timing protections | Autofill and keyboard-only use |

For a local demonstration, temporarily block a rare marker such as `MOSPARO_DEMO_BLOCAGE_2026`. Remove it afterwards. The everyday word `test` demonstrates matching but makes a poor production rule.

Avoid broad geography/provider bans without evidence: VPNs, mobile networks and shared addresses can represent legitimate users. Login usernames naturally repeat. Duplicate-submission restrictions that suit a contact form may reject returning users. Both plugins share one project, so its rules must accommodate both uses.

## Improvement workflow

1. Keep fictional legitimate samples: membership request, short question, international address, accented text and multiline paste.
2. Inspect actual spam and identify a specific pattern.
3. Add one clearly named rule and record why it exists.
4. Replay legitimate examples and a spam example.
5. Compare equivalent periods and inspect individual refusals.
6. Narrow or remove overbroad rules; record changes.

External rule packages need review of their source, quality and update schedule. [Rule packages](https://documentation.mosparo.io/docs/usage/rule_packages).

## Interpreting statistics

The dashboard charts valid and spam submissions. The selectable period depends on statistics retention; older submission details may already have expired. [Dashboard](https://documentation.mosparo.io/docs/usage/dashboard).

Submission details show scores, matched rules and server verification status. A green browser checkbox alone does not prove that Joomla verified the submission. Verification problems help diagnose field or signature mismatches. [Submissions](https://documentation.mosparo.io/docs/usage/submissions).

Track blocked share, reports from legitimate users and unwanted messages that actually reach you separately. Increased blocking can mean either an attack or an overbroad rule. “Percentage blocked” is not automatically detection accuracy: unreported false positives remain unknown.

## Additional settings and privacy

mosparo offers timing, honeypot, request limits and optional proof of work. Tune gradually and check scheduled cleanup and statistics retention. Submission details normally expire after 14 days. Explain the actual deployed processing to visitors. [Settings](https://documentation.mosparo.io/docs/usage/settings).

Self-hosting provides control over processing, but does not replace a privacy policy. Contact field values reach your mosparo instance. Only the username is analysed for logins. Keep MFA and login monitoring in place.

## News checked on 25 October 2026

The observed stable server is [mosparo 1.5.12](https://github.com/mosparo/mosparo/releases/tag/v1.5.12), published on 19 October 2026. The bundled [PHP API client is 2.0.1](https://github.com/mosparo/php-api-client/releases/tag/v2.0.1). Their versions are independent of this integration's 1.1.0.

[mosparo 1.5](https://mosparo.io/2026/06/17/mosparo-version-1-5-released/) added submission rules and multi-step forms; this does not automatically make every third-party multi-step form compatible with this package. [The detection rate](https://mosparo.io/2026/08/04/the-detection-rate/) explains the importance of rules. Features announced for 1.6 are not described here as already available.
