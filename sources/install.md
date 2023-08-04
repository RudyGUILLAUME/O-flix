# Commandes pour installer un framework complet a partir de skeleton

`composer create-project symfony/skeleton <monrep>`

Si on est sous apache il faut mettre en place la réecriture d'url

`composer require symfony/apache-pack`

Pour pouvoir créer nos routes avec les annotations

`composer require annotations`

Pour les Request et les Response

`composer require symfony/http-foundation`

Pour avoir accès à Twig

`composer require twig`

Et aux assets (asset())

`composer require symfony/asset`

Pour avoir la ligne profiler en bas

`composer require profiler --dev`

Pour les fonctions de dump

`composer require symfony/var-dumper --dev`

`composer require symfony/debug-bundle --dev`

# Pour installer un projet

## Avec les dépendances de dev

`composer install`

## Sans les dépendances de dev

`composer install --no-dev`

## Pour avoir accès aux makers de php bin/console

`composer require symfony/maker-bundle --dev`


# Pour regénérer un projet et une base de données après avoit fait un `git clone`

`git clone oflix-...`

Installer les dépendances projet

`composer install`

Il faut également vérifier le fichier .env ou .env.local qui doit contenir

`DATABASE_URL="mysql://oflix:oflix@127.0.0.1:3306/oflix?serverVersion=10.3.38-MariaDB&charset=utf8mb4"`

Vérifier la version de mysql avec `mysql -V` et mettre à jour la version dans le paramètre serverVersion.

Ensuite, supprimer la dabase existante

Utiliser adminer ou phpMyadmin et faire un drop de la database oflix

il faut créer l'utilisateur oflix ayant au minimum les droits sur la base oflix

Ensuite créer la dabatase

`php bin/console doctrine:database:create`

Executer les migrations existantes

`php bin/console doctrine:mig:migrate`

Normalement ici, la database est créée, il reste a charger les fixtures

`php bin/console doctrine:fixture:load`

# Pour la sécutité Symfony

Il faut charger un composant

`composer require symfony/security-bundle`
