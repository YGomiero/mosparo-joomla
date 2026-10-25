# Security / Sécurité

Report a suspected vulnerability privately to **extensions@gomiero.me**, with a minimal reproduction and affected versions. Do not include real credentials, private keys or visitor data in public issues. The maintained release line is 1.1.x; update Joomla, PHP and mosparo independently.

Signalez une vulnérabilité présumée en privé à **extensions@gomiero.me**, avec les versions et un scénario minimal. Ne publiez ni identifiants, ni clés, ni données de visiteurs. La branche maintenue est 1.1.x ; Joomla, PHP et le serveur mosparo se mettent à jour séparément.

The integration fails closed when verification fails or the server is unavailable. Administrators must retain a recovery route before enabling login protection. Use HTTPS with certificate verification. The private API key stays server-side but is stored in Joomla plugin parameters: protect database backups and administrator access.

La vérification refuse une soumission en cas d’échec ou d’indisponibilité de mosparo. Prévoir une récupération avant d’activer la protection des connexions. Utiliser HTTPS avec vérification du certificat. La clé privée reste côté serveur mais figure dans les paramètres Joomla : protéger les sauvegardes et l’administration.

This package does not replace MFA, authentication throttling, Joomla updates or a security audit. Passkeys, remembered authentication, API login and third-party authentication routes are outside its login gate.
