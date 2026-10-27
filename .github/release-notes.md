## Français

Après une vérification mosparo échouée ou expirée sur un formulaire Joomla valide, la connexion revient au formulaire avec un message explicatif, sur le site comme dans l’administration. Il suffit de renseigner à nouveau les identifiants et de refaire la vérification. La destination locale prévue après connexion est conservée, y compris pour confirmer un compte lié à une identité externe.

La connexion reste bloquée tant que la vérification n’a pas réussi. Les requêtes sans jeton Joomla valide restent refusées ; aucun mot de passe ni ancien jeton mosparo n’est conservé ou transmis dans l’adresse de retour.

Installer **pkg_mosparo-1.1.2.zip**, ou utiliser le gestionnaire de mises à jour Joomla. Les réglages sont conservés. Une instance mosparo déjà configurée, Joomla 6 et PHP 8.3+ restent nécessaires.

[Installation et dépannage](https://github.com/YGomiero/mosparo-joomla/blob/main/docs/installation.fr.md) · [Signaler un problème](https://github.com/YGomiero/mosparo-joomla/issues)

## English

When mosparo verification fails or expires on a valid Joomla form, site and administrator sign-ins return to a fresh login form with clear guidance. Re-enter your credentials and repeat verification. Safe local post-login destinations are retained, including external-account confirmation.

Authentication remains blocked until verification succeeds. Requests without a valid Joomla form token remain refused; passwords and old mosparo tokens are never stored or forwarded in the return address.

Install **pkg_mosparo-1.1.2.zip**, or use Joomla’s extension updater. Existing settings are retained. A configured mosparo instance, Joomla 6 and PHP 8.3+ are still required.

[Installation and troubleshooting](https://github.com/YGomiero/mosparo-joomla/blob/main/docs/installation.en.md) · [Report an issue](https://github.com/YGomiero/mosparo-joomla/issues)
