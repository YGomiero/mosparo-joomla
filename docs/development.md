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
3. Tag the reviewed commit with the manifest version (for example `1.1.0`).
4. Publish the ZIP, `update.xml` and `SHA256SUMS` together in the stable GitHub release.
5. Verify public downloads and Joomla update detection, then retain the final test record outside the source repository.

CI builds and uploads a workflow artifact; it does not automatically publish a release. Release publication is a deliberate maintainer action. JED Checker is a heuristic aid, not JED approval. Bundled upstream MIT files must not be falsely relabelled GPL to silence its alerts.
