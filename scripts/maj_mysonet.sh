#!/bin/bash

# Vérification si le script est exécuté avec les privilèges root
if [ "$(id -u)" != "0" ]; then
   echo "Ce script doit être exécuté en tant que root."
   exit 1
fi

# Cloner le dépôt Git à l'emplacement souhaité
git clone https://github.com/CebrailDevOps/spriv.git

echo "Les dossiers et fichiers ont été téléchargés avec succès depuis le dépôt Git."

# Déplacer le contenu du dossier mysonet vers /var/www/mysonet

sudo rm spriv/mysonet/db.php

sudo mv /var/www/mysonet/db.php spriv/mysonet

sudo rm /var/www/mysonet/*

sudo mv spriv/mysonet/* /var/www/mysonet

sudo rm -r spriv

echo "Les fichiers ont été déplacés avec succès."

### Donner les droits à www-data pour /var/www/mysonet
sudo chmod -R 755 /var/www/mysonet
sudo chown -R www-data:www-data /var/www/mysonet