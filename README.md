# Bookineo Backend

Bookineo est une application web de **location de livres entre particuliers**.  
Cette partie correspond au **backend**, développé en **Symfony**, avec une base de données **MySQL (phpMyAdmin)**.  

---

## Technologies utilisées

- [Symfony 6+](https://symfony.com/) – Framework PHP
- [Doctrine ORM](https://www.doctrine-project.org/) – Gestion de la base de données
- [MySQL](https://www.mysql.com/) + [phpMyAdmin](https://www.phpmyadmin.net/) – Base de données
- [Composer](https://getcomposer.org/) – Gestion des dépendances

---

## Installation et lancement

1. Cloner le dépôt 
git clone https://github.com/ton-profil/bookineo-back.git
cd bookineo-back

2. Installer les dépendances
composer install

3. Configurer l’environnement (Copier le fichier .env en .env.local et adapter la configuration)
DATABASE_URL="mysql://user:password@127.0.0.1:3306/bookineo"

4. Créer la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

5. Lancer le serveur Symfony
symfony server:start
