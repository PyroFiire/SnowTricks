SnowTricks
=====================

Projet 6 du parcours PHP / Symfony sur OpenClassrooms - Développez de A à Z le site communautaire SnowTricks

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/3f10def50b134d508b2cd56b13555069)](https://www.codacy.com/manual/PyroFiire/SnowTricks?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=PyroFiire/SnowTricks&amp;utm_campaign=Badge_Grade)

INSTRUCTION
-----------

GIT AND COMPOSER
--------------------
use "git clone https://github.com/PyroFiire/SnowTricks.git" in a folder in your server or local for download the project.
use "cd SnowTricks" for go in the project
use "composer install" for install dependancy

CONFIG.PHP
----------

Copie the file .env by .env.local and define your connection to the database (l.27) and smtp

FIXTURES
--------

use "php bin/console doctrine:fixtures:load --group start" for start the project with 10 tricks and a admin account. Notice : You need developpement environnement for use this commande
you can load more fixtures for developpement with --group test.

ENVIRONNEMENT
-------------

change APP_ENV=dev line 17 for APP_ENV=prod

ACCOUNTS
--------

admin connection :
username : admin
email : admin@admin.com
password : adminadmin
