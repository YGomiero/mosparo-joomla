# Installer et configurer

[English](installation.en.md) · [Accueil](../README.fr.md)

## Préparer mosparo

Il faut une instance mosparo opérationnelle, joignable en HTTPS par le navigateur des visiteurs **et** par PHP sur le serveur Joomla. Son installation n’est pas incluse dans le paquet. Créer un projet et renseigner les noms de domaine autorisés, sans chemin : par exemple `www.exemple.fr` et `exemple.fr` si les deux sont utilisés. Copier son UUID, sa clé publique et sa clé privée dans le plugin Joomla. La clé privée ne doit jamais figurer dans une capture publique, un dépôt ou un ticket.

Le serveur PHP doit disposer de cURL, JSON et des extensions nécessaires à Joomla. Le paquet contient ses bibliothèques : aucune exécution de Composer n’est nécessaire sur le site destinataire.

## Installer le paquet

Sauvegarder fichiers et base. Installer le ZIP `pkg_mosparo-1.1.3.zip` via l’installateur Joomla. Le paquet regroupe deux plugins et une seule source de mise à jour. Ne pas installer séparément les ZIP internes. La désinstallation s’effectue sur le paquet et retire les deux plugins ; elle ne supprime pas le projet sur le serveur mosparo.

Sur un site possédant déjà `captcha/mosparo` 1.0.2, l’élément et son espace de noms sont conservés pour permettre la mise à niveau. Contrôler les réglages après installation. Le site source utilisé pour le développement n’a pas été modifié.

## Réglages CAPTCHA

| Réglage | Utilisation |
|---|---|
| URL | Racine de mosparo, éventuellement avec un sous-dossier ; pas d’URL d’administration ni de paramètres |
| UUID / clé publique | Identifient le projet ; visibles dans la configuration du widget |
| Clé privée | Réservée à la vérification serveur ; stockée dans les paramètres Joomla |
| Charger les CSS | Laisser activé pour utiliser l’apparence du projet mosparo |
| Vérifier SSL | Laisser activé en exploitation |
| Certificat CA | Facultatif : chemin absolu vers un certificat/ensemble de CA de confiance, utile en développement local |
| Champs requis | Profil de repli pour un formulaire tiers ; un nom HTML exact par ligne, cohérent avec les champs réellement soumis |

Les profils natifs sont déterminés côté serveur. Le contact standard utilise `jform[contact_name]`, `jform[contact_email]`, `jform[contact_subject]`, `jform[contact_message]`. Les noms contenant `password`, `passwd`, `secret` ou `token` sont refusés dans les profils. Un champ absent ou une valeur de structure inattendue invalide la vérification. Joomla conserve ses propres contrôles de saisie et de session.

## Contact, page et module de connexion

Dans les options de Contacts, choisir **mosparo** pour CAPTCHA ; vérifier qu’un réglage propre au contact n’impose pas un autre choix. Publier un contact et son lien de menu. Tester une phrase normale, puis une règle de démonstration temporaire.

Pour la connexion, activer le plugin Système et **Protéger les connexions du site**. Le widget apparaît sur le composant Utilisateurs et les modules Connexion natifs, y compris plusieurs formulaires sur une page. Il n’est pas nécessaire de modifier les fichiers Joomla ou de créer une substitution de template.

## Administration et récupération d’accès

Tester d’abord le site. Garder une session administrateur ouverte, activer **Protéger la connexion à l’administration**, puis essayer dans une autre session. Conserver un accès aux fichiers/base : si mosparo devient indisponible, les connexions protégées sont refusées.

En cas de blocage, un administrateur du serveur peut désactiver uniquement le plugin de connexion dans la base, en remplaçant `PREFIX_` par le préfixe réel :

```sql
UPDATE PREFIX_extensions SET enabled = 0
WHERE type = 'plugin' AND folder = 'system' AND element = 'mosparologin';
```

Après réparation de mosparo ou des certificats, réactiver le plugin et refaire les essais. Les protections Joomla (mot de passe, MFA, session, limitation des tentatives) restent nécessaires.

## Dépannage

- **Widget absent** : vérifier activation, options de protection, scripts bloqués et erreurs du navigateur. Une substitution de formulaire tiers peut demander une adaptation.
- **Erreur après une case verte** : vérifier les clés, le certificat de confiance de PHP, l’accès sortant et les champs vérifiés. Dans mosparo, consulter la soumission et ses problèmes de vérification.
- **Vérification échouée ou expirée à la connexion** : le formulaire se recharge avec une explication. Renseigner à nouveau ses identifiants et refaire la vérification mosparo. Aucune connexion n’est ouverte après le refus ; le mot de passe et les anciens jetons ne sont ni conservés ni retransmis. Si la session Joomla a elle-même expiré ou si la requête est invalide, un refus 403 reste possible : ouvrir de nouveau le formulaire.
- **Un message légitime est bloqué** : examiner les règles et le score dans mosparo, puis ajuster la règle responsable.
- **Configuration locale HTTPS** : installer une CA de confiance côté navigateur et PHP ; le champ CA évite de désactiver la vérification TLS dans le plugin.

Configurer également la politique CSP du site pour autoriser les ressources et les connexions à l’origine mosparo. L’extension ne modifie pas la CSP du site.

## Données et mises à jour

Le navigateur envoie les champs protégés au serveur mosparo, avec les informations techniques propres à mosparo. La vérification PHP utilise les empreintes/signatures du client officiel. Informer les visiteurs du traitement, de l’hébergement et de la conservation effectivement configurés. Pour les connexions, seul `username` est analysé ; aucune copie du mot de passe n’est transmise à mosparo par cette intégration.

Utiliser **Système → Mise à jour → Extensions**. Le serveur de mise à jour du paquet doit être activé et GitHub accessible depuis PHP. Sauvegarder avant une mise à niveau. La documentation du dépôt peut évoluer : consulter le tag correspondant à votre version pour retrouver son état exact.
