# Development / Développement

## Architecture

`captcha/mosparo` implements Joomla 6's CAPTCHA provider interface. `FormData` selects server-owned field profiles and preserves raw field values for canonical signing. `Renderer` shares assets between widgets and exports only public settings. `Verifier` calls the pinned official PHP API client and checks both the signed result and each required field.

`system/mosparologin` checks native password login routes at `onAfterRoute`, before authentication. The site and administrator switches are independent. Its JavaScript discovers native forms, including the Joomla 6 SEF action on the Users login page. It filters submitted names explicitly: changing a password input to text does not include it in mosparo data.

Le fournisseur CAPTCHA, la sélection des champs, le rendu et la vérification sont séparés. Le plugin Système vérifie les routes de connexion avant l’authentification. Les erreurs de service ou de configuration refusent la validation, sans exposer les clés ou les données dans les journaux. Le client amont conserve sa préparation canonique des données : ne pas ajouter de filtrage ou de nettoyage qui changerait la signature.

## Build / Construction

Requirements: PHP 8.3+, Composer 2, ZIP extension. Install the exact locked dependencies; do not run an unreviewed dependency update before releasing.

```sh
composer install --no-dev --prefer-dist --no-interaction
php tools/build.php
php tests/run.php /path/to/joomla
```

`dist/pkg_mosparo-1.1.0.zip` is the installable artifact. Runtime dependencies and original licences are included; upstream tests/documentation/development files are excluded. Source GitHub archives lack installed dependencies and the package's nested ZIPs. File ordering and ZIP timestamps are fixed; compare builds using the same PHP/ZIP toolchain.

Le constructeur produit le ZIP installable, le flux Joomla et la somme SHA-256. Les versions se trouvent dans les trois manifestes, le chargeur d’assets et la documentation. Le flux utilise un téléchargement versionné ; sa copie stable est un asset de la dernière release. Ne jamais remplacer silencieusement le ZIP d’une version publiée : créer une nouvelle version.

## Tests

The automated regression suite checks raw Unicode/whitespace, missing or malformed fields/tokens, sensitive-name exclusion, URL restrictions and official API response signatures. It requires the libraries of a Joomla installation, but no database or credentials. CI downloads Joomla 6.2.0 for these libraries.

Manual acceptance: clean install, upgrade/settings preservation, contact accepted and blocked by a rule, native page/module/admin login, password reveal, multiple widgets, server rejection without a valid token, unavailable mosparo, private-key absence from HTML, update feed/download checksum and JED Checker review. A CAPTCHA test does not validate mail delivery or every third-party template.

La recette de publication doit couvrir les quatre surfaces, les refus côté serveur et le maintien des réglages après mise à niveau. Les captures publiques doivent utiliser des données fictives. Ne jamais publier une configuration Joomla, une base de données, un cookie, une clé privée ou un rapport contenant des données de visiteurs.

## Release

1. Update versions and changelog, review dependency changes and licences.
2. Run CI and local integration checks against the exact final ZIP.
3. Update `.github/release-notes.md`, then tag the reviewed commit with the manifest version (for example `1.1.1`). Pushing a new stable numeric tag triggers publication.
4. `Publish stable release` runs the shared test/build workflow, verifies the package identity and checksum, then creates the release with ZIP, `update.xml` and `SHA256SUMS`. An existing release is never overwritten by this workflow.
5. Verify public downloads and Joomla update detection, then retain the final test record outside the source repository.

The `Joomla update feed` workflow also handles a stable release published manually: it downloads the actual installable ZIP, checks its manifest/version, computes its SHA-256 and uploads its feed and checksum. It never replaces the ZIP. A release created with GitHub's workflow token does not trigger a second workflow; the tag workflow therefore includes the metadata itself. Prereleases are excluded.

Both release workflows can be started manually from **Actions → Run workflow** as a rehearsal. Manual runs only validate and produce artifacts; they do not publish or change a release. The build workflow also runs on main and pull requests. No custom token is needed: the publishing jobs use GitHub's built-in token with repository contents write permission.

The feed explicitly identifies the package as `pkg_mosparo`, type `package`, **client_id 0**. Without this value Joomla defaults to administrator client 1, so the update may be downloaded but never associated with the installed package. The regression check verifies this identity, the versioned download URL and the actual ZIP hash.

En français : mettre à jour les trois manifestes, le numéro d’asset, l’historique et les notes de release, puis pousser un nouveau tag stable correspondant à la version. L’action construit, teste et publie les trois fichiers de distribution. Une release manuelle déclenche aussi la génération du flux depuis son ZIP. Les lancements manuels des actions sont des essais sans publication. Le propriétaire du dépôt garde la maîtrise de la publication en créant le tag ; ne jamais déplacer un tag déjà publié.

La **JED et le gestionnaire de mises à jour Joomla sont distincts** : ces actions alimentent Joomla. Elles ne créent pas une fiche JED et ne modifient pas automatiquement son numéro de version. Après acceptation de la fiche, le mainteneur doit actualiser ses informations dans la JED lors des publications. JED Checker est une aide heuristique, pas une approbation JED. Ne pas réétiqueter GPL les fichiers MIT des bibliothèques pour masquer des alertes.
