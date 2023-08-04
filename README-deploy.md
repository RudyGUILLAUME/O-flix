# deploy

## Checklist deploy

Objectif mettre notre site symfony en ligne

on se déplace dans le bon dossier pour faire notre `git clone`

```bash
cd /var/www/html
git clone git@github.com:xxxxxxxxxx.git
```

on descend dans le dossier de notre appli et on lance le composer install

```bash
cd xxxxx
composer install
```

on édite notre fichier `.env.local` pour parametrer notre appli.

```bash
sudo nano .env.local
```

on remplit notre fichier de paramétrage, on sauvegarde avec les touches `ctrl+o` puis `enter` et on ferme avec `ctrl+x`

Notre appli est prête, on demande à doctrine de créer la base

```bash
bin/console d:d:c
bin/console doctrine:migrations:migrate
bin/console d:f:l -n --group=AppFixtures
```

on lance notre commande pour mettre a jour les slug

```bash
bin/console oflix:movies:slugify
```

Si le site ne s'affiche pas et que les routes ne fonctionne pas, il faut dire à Apache d'autoriser la réécriture d'URL

```bash
sudo nano /etc/apache2/apache2.conf
```

On scroll jusqu'à Directory...
On modifie ça :

```text
<Directory /var/www/>
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
</Directory>
```

par :

```text
 <Directory /var/www/>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
</Directory>
 ```

Pour que mon appli soit rapide, on peut clear le cache avec la commande

```bash
APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
```

il nous manque plus que le token JWT

```bash
bin/console lexik:jwt:generate-keypair
```

## PB avec MySQL

je modifie le mot de passe de explorateur :

```SQL
ALTER USER 'explorateur'@'localhost' IDENTIFIED BY 'Ereul9Aeng';
```

je crée l'utilisateur explorateur

```SQL
CREATE USER 'explorateur' IDENTIFIED BY 'Ereul9Aeng';
GRANT ALL PRIVILEGES ON *.* TO 'explorateur'@'localhost';
```

Pour régler des problèmes de droits

```shell
sudo chown -R student.www-data /var/www
sudo chmod -R ug+rwx /var/www
```

Pour passer le site en production

Il faut changer la viariable `APP_ENV=prod` dans le fichier .env.local

Si on se reconnecte sur le site le profiler à disparu

Pour finaliser le passage en production, il faut maintenant faire la commande

```shell
composer install --no-dev
```
Si a part la page home, il n'y a que des erreurs 404 cela veut dire que la 
réécriture d'URL ne fonctionne pas
- Vérifier le .htaccess dans public
- vérifier que la récriture d'URL est autorisée sur apache


```
cd /etc/apache2/sites-available/
sudo nano 000-default.conf
```
rajouter ce bloc à la fin à l’intérieur et à la fin du bloc VirtualHost *:80

```xml
# Configuration du répertoire
<Directory "/var/www/html">
    # AllowOverride All permet d'autoriser la surcharge de la configuration d'Apache via .htaccess
    # Question de sécurité : on ne fait pas n'imoprte quoi avec la conf du serveur
    AllowOverride All
    Order allow,deny
    Allow from All
</Directory>
```

Afin d'obtenir ceci :

```xml
<VirtualHost *:80>

    # ...

    DocumentRoot /var/www/html

    <Directory /var/www/html>
        AllowOverride All
        Order Allow,Deny
        Allow from All
    </Directory>

</VirtualHost>
```

Puis faire un restart apache

```shell
sudo service apache2 restart
```