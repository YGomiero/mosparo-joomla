# Changelog / Historique

## 1.1.2 — 2026-10-27

- Return legitimate site and administrator login forms to a fresh form with translated guidance after failed or expired mosparo verification. Authentication remains blocked; invalid Joomla form tokens and non-POST requests remain refused.
- Preserve safe local post-login destinations, including an external-account confirmation, without storing or forwarding passwords or old mosparo tokens.
- Après un échec ou une expiration mosparo, retour au formulaire de connexion avec un message clair. Les contrôles de sécurité restent actifs et la destination locale prévue est conservée.

## 1.1.1 — 2026-10-25

- Align the installed package name, update feed and release title with the JED listing name: mosparo. No change to protection behaviour.
- Harmonisation du nom du paquet installé, du flux et de la release avec la fiche JED : mosparo. Aucun changement de fonctionnement.

## 1.1.0 — 2026-10-25

- Package with CAPTCHA and optional native site/module/administrator login protection; Joomla package update feed with SHA-256.
- Official mosparo PHP API client 2.0.1 with locked dependencies; strict field verification and private-key isolation.
- Raw-field signing, multiline/Unicode preservation, explicit password exclusion, multiple widgets and Joomla 6 SEF login-page support.
- TLS verification by default, optional trusted CA bundle, bounded API timeouts and fail-closed responses.
- Complete French/English settings and documentation; installation/recovery guide, rule tuning, statistics and native CAPTCHA comparison.

- Paquet regroupant CAPTCHA et protection facultative des connexions natives du site, des modules et de l’administration ; mises à jour Joomla avec SHA-256.
- Client PHP officiel 2.0.1, dépendances verrouillées, vérification des champs et exclusion explicite des mots de passe.
- Prise en charge des valeurs brutes, accents, retours à la ligne, widgets multiples et connexion avec URL SEF.
- Vérification TLS par défaut, CA personnalisée facultative et délais d’attente bornés.
- Documentation et interface français/anglais, règles, statistiques, comparaison Joomla et récupération d’accès.

## 1.0.2 — 2026-10-08

Existing local CAPTCHA plugin used as the starting point. No public release of that version is asserted here.

Plugin CAPTCHA local servant de base à cette reprise ; aucune release publique de cette version n’est revendiquée ici.
