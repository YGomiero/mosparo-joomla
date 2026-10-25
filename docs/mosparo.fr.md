# Pourquoi mosparo ? Des règles et des statistiques

[English](mosparo.en.md) · [Accueil](../README.md)

## Le projet

[mosparo](https://mosparo.io) est un logiciel libre de protection contre le spam, porté par une association suisse. On l’installe sur son propre hébergement ou on choisit un service hébergé compatible. Joomla utilise ce serveur pour analyser les formulaires et vérifier leur validation. Le code du projet mosparo est sous licence MIT ; cette intégration Joomla est indépendante.

## Ce qui change par rapport au CAPTCHA livré avec Joomla

Joomla 6.1 a réintroduit un CAPTCHA natif fondé sur ALTCHA et une preuve de travail. Il est présent dans Joomla 6.2. Il serait donc inexact de présenter le CAPTCHA actuel de Joomla comme un service Google imposé. [Présentation officielle Joomla](https://magazine.joomla.org/issues/2026/january-2026/built-in-captcha-returning-to-joomla-core).

| Besoin | CAPTCHA natif de Joomla 6.2 | mosparo avec ce paquet |
|---|---|---|
| Limiter les soumissions automatisées | Preuve de travail calculée dans le navigateur | Analyse du contenu et protections configurées dans mosparo |
| Adapter le filtrage à ses spams | Pas de gestionnaire de règles de contenu comparable dans le plugin | Règles adaptées aux messages et au contexte du site |
| Comprendre les refus | Pas de tableau de soumissions mosparo | Historique, détails des règles et statistiques dans mosparo |
| Hébergement | Intégré à Joomla | Service mosparo supplémentaire à maintenir |
| Connexions natives | L’activation d’un CAPTCHA global ne suffit pas à protéger tous les logins | Plugin Système dédié, activations site/admin séparées |

Le choix dépend du besoin. Pour un formulaire simple et une maintenance minimale, le CAPTCHA natif constitue une option pertinente. mosparo devient intéressant lorsqu’on veut comprendre les spams reçus, ajuster des règles et observer leur effet. Une règle mal choisie peut bloquer un vrai visiteur ; les statistiques accompagnent une vérification humaine.

## Démarrer avec des règles utiles

Les règles de mosparo attribuent des points ; le seuil du projet détermine le classement en spam. Les types couvrent notamment textes, adresses, domaines, URL et informations techniques. Le mode de correspondance compte : un fragment très court n’équivaut pas à un mot entier. [Types de règles](https://documentation.mosparo.io/docs/usage/rule_types), [règles](https://documentation.mosparo.io/docs/usage/rules).

Les exemples suivants sont des **propositions de réglage à tester**, pas une configuration universelle. Ne recopier aucun domaine fictif comme véritable liste de blocage.

| Situation observée | Essai conseillé | Contrôle avant généralisation |
|---|---|---|
| Une campagne répète une expression très caractéristique | Règle de texte précise, nommée avec sa date et sa raison | Essayer des demandes légitimes qui emploient des mots voisins |
| Des publicités répètent la même adresse de site | Règle ciblée sur ce domaine/URL | Vérifier qu’un adhérent ne peut pas le citer légitimement |
| Une boîte identifiée envoie plusieurs abus | Règle sur l’adresse exacte | Prévoir une réévaluation plutôt que bloquer un fournisseur entier |
| Des textes présentent une structure régulière | Expression régulière courte, documentée et testée | Tester accents, apostrophes, retours à la ligne et messages longs |
| Une séquence automatisée remplit trop vite le formulaire | Ajuster les protections temporelles mosparo | Essayer le remplissage automatique et le clavier |

Pour une démonstration locale, créer une règle sur un marqueur rare, par exemple `MOSPARO_DEMO_BLOCAGE_2026`, puis la retirer après l’essai. Le mot courant `test` est pratique pour montrer un blocage, mais constitue une mauvaise règle de production : un visiteur peut légitimement l’écrire.

Éviter les blocages géographiques ou d’hébergeurs sans analyse : VPN, réseaux mobiles et adresses partagées créent des cas légitimes. Ne pas appliquer automatiquement les règles du contact aux connexions. Un identifiant répété est normal ; une politique sur les soumissions identiques peut empêcher un utilisateur de revenir. Ce paquet partage un seul projet entre ses deux plugins : choisir des règles compatibles avec les deux usages.

## Une méthode d’amélioration

1. Conserver un petit jeu de messages légitimes fictifs : demande d’adhésion, question courte, adresse internationale, texte accentué, copier-coller avec sauts de ligne.
2. Observer les spams réellement reçus et choisir un motif précis.
3. Ajouter une seule règle, avec un nom compréhensible et la raison de son ajout.
4. Rejouer les exemples légitimes et un exemple indésirable.
5. Comparer une période avant/après, puis regarder les refus individuellement.
6. Corriger ou retirer une règle trop large. Noter la date du changement.

Les paquets de règles permettent de suivre des listes externes ; leur qualité, leur provenance et leur actualisation doivent être examinées. [Documentation des paquets](https://documentation.mosparo.io/docs/usage/rule_packages).

## Lire les statistiques sans se tromper

Le tableau de bord présente les soumissions valides et les spams dans le temps. Sa période dépend de la conservation des statistiques ; les détails des anciennes soumissions peuvent déjà avoir disparu. [Tableau de bord](https://documentation.mosparo.io/docs/usage/dashboard).

Dans une soumission, examiner la règle déclenchée, son score et le statut de vérification serveur. Une case verte dans le navigateur ne prouve pas à elle seule que Joomla a validé la soumission. Les problèmes de vérification aident à diagnostiquer une divergence de champs ou de signature. [Soumissions](https://documentation.mosparo.io/docs/usage/submissions).

Pour votre suivi, distinguer trois indicateurs : part de soumissions bloquées, signalements de visiteurs légitimes et messages indésirables effectivement reçus. Une hausse des blocages peut provenir d’une attaque ou d’une règle trop stricte. Le « taux bloqué » n’est pas une mesure automatique de la précision du filtre : il manque notamment les faux positifs non signalés.

## Réglages complémentaires et confidentialité

mosparo propose des protections telles que le délai minimal, le honeypot, les limites de requêtes et une preuve de travail facultative. Les régler progressivement ; vérifier le nettoyage planifié et la conservation des statistiques. Le détail des soumissions est normalement supprimé après 14 jours. Les visiteurs doivent être informés du traitement réellement déployé. [Réglages](https://documentation.mosparo.io/docs/usage/settings).

Le contrôle des données est un intérêt de l’auto-hébergement, pas une dispense de politique de confidentialité. Les champs du contact sont transmis à votre instance mosparo. Pour la connexion, l’intégration se limite à l’identifiant. La protection ne remplace pas l’authentification multifacteur ni la surveillance des tentatives de connexion.

## Actualité vérifiée le 25 octobre 2026

Le serveur stable observé est [mosparo 1.5.12](https://github.com/mosparo/mosparo/releases/tag/v1.5.12), publié le 19 octobre 2026 ; le client embarqué est [PHP API client 2.0.1](https://github.com/mosparo/php-api-client/releases/tag/v2.0.1). Leurs numéros sont indépendants de la version 1.1.0 de ce paquet.

La [version 1.5](https://mosparo.io/2026/06/17/mosparo-version-1-5-released/) a notamment apporté des règles de soumission et les formulaires à étapes. Cela ne signifie pas que tout formulaire tiers à étapes soit automatiquement pris en charge ici. L’article [The detection rate](https://mosparo.io/2026/08/04/the-detection-rate/) rappelle que l’efficacité dépend des règles. Les évolutions annoncées pour 1.6 ne sont pas présentées comme des fonctions de la version installée.
