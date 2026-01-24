# API-store

L'API web + l'UI admin

## A installer au préalable

Seulement Docker.

## Préparer et lancer le serveur

Dans un terminal :
1. Allez dans le dossier racine de ce dépôt
1. Pull les images Docker : `docker compose pull --ignore-buildable`
1. Build les images Docker depuis le Dockerfile : `docker compose build`
1. Dans le fichier `.env`, modifiez les ports si ils sont déjà utilisés (serveur, base de données et mailer)
1. Lancez le serveur : `docker compose up -d`
1. Génerez des clés pour signer les JWT : `docker compose exec -u $(id -u):$(id -g) php bin/console lexik:jwt:generate-keypair`
1. Exécutez les migrations pour ajouter les tables dans la base de données : `docker compose exec php bin/console doctrine:schema:update --force`
1. Ajoutez des données de test : `docker compose exec php bin/console app:seed-test-data`

## Commandes utiles

**Vérifier si la structure de la BDD a besoin d'être mise à jour** : `docker compose exec php bin/console doctrine:schema:validate`

**Mettre à jour la structure de la BDD** : `docker compose exec php bin/console doctrine:schema:update --force`

**Mettre jour les données de test** :
- Vider toutes les tables :
    - `docker compose exec php bin/console doctrine:schema:drop --force`
    - `docker compose exec php bin/console doctrine:schema:update --force`
- Ajouter les données de test : `docker compose exec php bin/console app:seed-test-data`

# Dump

## Tables insertion order

1. employee
1. vat_rate
1. category
1. product
1. local_sale
1. local_sale_item
1. correction
1. correction_item
1. destruction
1. destruction_item
1. purchase
1. purchase_item
1. mobile_sale
1. mobile_sale_item
