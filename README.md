# MoveEasy - Application de Gestion de Mobilité Accessible

## Description du Projet
**MoveEasy** est une application web complète visant à faciliter l'accessibilité et la mobilité des personnes, en particulier celles en situation de handicap.  
L'application permet aux utilisateurs de :  
- Consulter les **trains accessibles** avec leurs **équipements adaptés**
- **Réserver** des billets facilement
- **Consulter les itinéraires et stations**
- **Laisser des avis** et **soumettre des réclamations**
- Bénéficier d'interfaces **modernes, intuitives et stylées**

Le projet s’adresse à la fois aux utilisateurs finaux et aux administrateurs du système via des interfaces dédiées.

---

## Table des Matières
- [Installation](#installation)
  - [Prérequis](#prérequis)
  - [Étapes d'Installation](#étapes-dinstallation)
- [Configuration de l'Environnement](#configuration-de-lenvironnement)
  - [Installation de PHP](#installation-de-php)
  - [Installation de Symfony](#installation-de-symfony)
- [Utilisation](#utilisation)
  - [Rôles et Permissions](#rôles-et-permissions)
- [Contribution](#contribution)
  - [Contributeurs](#contributeurs)
  - [Comment Contribuer](#comment-contribuer)
- [Licence](#licence)

---


## Installation

### Prérequis
Avant d'installer et de lancer l'application, assurez-vous d'avoir installé les éléments suivants :
- **PHP** (version 7.4 ou supérieure)
- **Symfony** (version 5.4 ou supérieure)
- **MySQL** (ou une autre base de données compatible)
- **Composer** (pour la gestion des dépendances PHP)


### Étapes d'Installation
1. Clonez le repository :
    ```bash
    git clone https://github.com/username/MoveEasy.git
    cd MoveEasy
    ```

2. Installez les dépendances PHP via Composer :
    ```bash
    composer install
    ```

3. Configurez votre base de données dans le fichier `.env` (exemple avec MySQL) :
    ```env
    DATABASE_URL="mysql://root:password@127.0.0.1:3306/move_easy_db"
    ```

4. Créez la base de données et exécutez les migrations :
    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    ```

5. Lancez le serveur Symfony :
    ```bash
    symfony server:start
    ```

### Accès à l'application
Une fois l'application lancée, accédez à l'interface dans votre navigateur via :
http://localhost:8000


---

## Utilisation

## Installation de PHP

Pour utiliser ce projet, vous devez installer PHP. Voici les étapes :

1. Téléchargez PHP à partir du site officiel : [PHP - Téléchargement](https://www.php.net/downloads.php).

2. Installez PHP en suivant les instructions spécifiques à votre système d'exploitation :

   - **Windows** : Utilisez [XAMPP](https://www.apachefriends.org/fr/index.html) ou [WampServer](http://www.wampserver.com/).
   
   - **macOS** : Avec [Homebrew](https://brew.sh/) :
     ```bash
     brew install php
     ```
   
   - **Linux** (Ubuntu/Debian) :
     ```bash
     sudo apt update
     sudo apt install php
     ```

3. Vérifiez l'installation :
   ```bash
   php -v

## Installation de Symfony

Pour développer avec Symfony, suivez ces étapes :

1. **Prérequis** :  
   - PHP 8.2 ou supérieur ([voir l'installation PHP](#installation-de-php))
   - Composer (gestionnaire de dépendances) :  
     ```bash
     # Linux/macOS
     curl -sS https://getcomposer.org/installer | php
     sudo mv composer.phar /usr/local/bin/composer

     # Windows (via PowerShell)
     Set-ExecutionPolicy Bypass -Scope Process -Force
     iex ((New-Object System.Net.WebClient).DownloadString('https://getcomposer.org/installer.ps1'))
     ```

2. **Installer Symfony CLI** (outil officiel) :  
   ```bash
   # Linux/macOS
   curl -sS https://get.symfony.com/cli/installer | bash
   export PATH="$HOME/.symfony5/bin:$PATH"

   # Windows (via Chocolatey)
   choco install symfony-cli     
### Rôles et Permissions
- **Administrateur** : Accès à la gestion des trains, des équipements, des avis et réclamations.
- **Utilisateur Standard** : Consultation des trains, réservation, soumission d'avis et réclamations.

---


# Contributions

Nous remercions tous ceux qui ont contribué à ce projet !

## Contributeurs

Les personnes suivantes ont contribué à ce projet en ajoutant des fonctionnalités, en corrigeant des bugs ou en améliorant la documentation :

- [dorra jemili](https://github.com/dorrajem) 
- [malek rahal](https://github.com/Maleek-Rahal) 
- [iadh hammemi](https://github.com/Iyadh3)
- [mouhamed elyas ayechi](https://github.com/Elyes-Ayachi)
- [kenza hmad](https://github.com/kenza-44)
- [lina boufaied](https://github.com/kenza-44)

Si vous souhaitez contribuer, suivez les étapes ci-dessous pour faire un **fork**, créer une nouvelle branche et soumettre une **pull request**.

## Comment contribuer ?

1. **Fork le projet** : Allez sur la page GitHub du projet et cliquez sur le bouton **Fork** dans le coin supérieur droit pour créer une copie du projet dans votre propre compte GitHub.

2. **Clonez votre fork** : Clonez le fork sur votre machine locale :
   ```bash
   git clone https://github.com/votre-utilisateur/MoveEasy24-25-Symfony.git
   cd projet
## Licence

Ce projet est sous la licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

