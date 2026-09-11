# Documentation du projet UniReserve

## 1. Présentation

UniReserve est une application PHP de gestion des salles universitaires et de leurs réservations. Elle permet de consulter les salles, de gérer leur état, de créer des réservations et d'annuler une réservation existante.

Le projet est développé sans framework PHP complet. Il assemble plusieurs composants spécialisés :

- FastRoute pour le routage HTTP ;
- PHP-DI pour l'injection de dépendances ;
- Eloquent pour l'ORM et les relations ;
- Respect/Validation pour la validation des formulaires ;
- Dotenv pour la configuration ;
- PHPUnit pour les tests ;
- Docker et MySQL pour l'exécution.

L'application ne contient pas de JavaScript applicatif. Les interactions sont réalisées avec PHP, HTML, CSS, les formulaires HTTP et les redirections PRG.

## 2. Fonctionnalités

### 2.1 Gestion des salles

Un utilisateur peut :

- consulter la liste des salles ;
- voir le nombre total de salles ;
- voir le nombre de salles actives ;
- voir la capacité totale ;
- afficher le détail d'une salle ;
- créer une salle ;
- modifier une salle ;
- activer ou désactiver une salle ;
- voir les réservations liées à une salle.

Une salle possède les propriétés suivantes :

| Propriété | Type | Description |
| --- | --- | --- |
| `id` | entier | Identifiant unique |
| `nom` | chaîne | Nom de la salle |
| `batiment` | chaîne | Bâtiment ou emplacement |
| `capacite` | entier | Nombre de places |
| `type` | chaîne | Type autorisé |
| `active` | booléen | Indique si la salle peut être réservée |
| `created_at` | date | Date de création |
| `updated_at` | date | Date de modification |

Les types autorisés sont `cours`, `informatique`, `laboratoire`, `amphitheatre` et `reunion`.

### 2.2 Gestion des réservations

Un utilisateur peut :

- consulter toutes les réservations ;
- filtrer les réservations par salle ;
- consulter le détail d'une réservation ;
- créer une réservation ;
- annuler une réservation confirmée.

Une réservation possède les propriétés suivantes :

| Propriété | Type | Description |
| --- | --- | --- |
| `id` | entier | Identifiant unique |
| `salle_id` | entier | Salle réservée |
| `responsable` | chaîne | Nom du responsable |
| `email` | chaîne | Adresse électronique |
| `motif` | chaîne | Raison de la réservation |
| `date_debut` | date | Début du créneau |
| `date_fin` | date | Fin du créneau |
| `statut` | chaîne | `confirmée` ou `annulée` |
| `created_at` | date | Date de création |
| `updated_at` | date | Date de modification |

## 3. Règles métier

Une réservation est acceptée seulement si :

1. la salle existe ;
2. la salle est active ;
3. le responsable contient entre 2 et 120 caractères ;
4. l'adresse électronique est valide ;
5. le motif contient entre 5 et 255 caractères ;
6. la date de début précède la date de fin ;
7. la durée est inférieure ou égale à quatre heures ;
8. la date de début est dans le futur ;
9. aucune réservation confirmée ne chevauche le créneau.

Deux périodes se chevauchent lorsque :

```text
nouveauDebut < reservationExistante.dateFin
ET
nouvelleFin > reservationExistante.dateDebut
```

Une réservation voisine ne constitue pas un conflit. Par exemple, `10:00-12:00` et `12:00-14:00` sont compatibles. Une réservation annulée ne bloque plus la salle.

La création est exécutée dans une transaction. Le repository verrouille la salle avec `findForUpdate()` afin de réduire le risque de réservations concurrentes pour la même salle.

## 4. Architecture générale

Le projet suit une architecture MVC en couches :

```text
Navigateur
   |
   v
public/index.php                 Front Controller
   |
   v
FastRoute                          Routage
   |
   v
Controller                         Requête HTTP et réponse
   |
   +--> Validator                  Validation syntaxique
   |
   +--> DTO                        Données typées
   |
   +--> Service                    Règles métier
   |
   +--> Repository Interface       Contrat d'accès aux données
            |
            v
       Eloquent Repository          Implémentation MySQL
            |
            v
          Modèles Eloquent          Salle et Reservation
```

### 4.1 Front Controller

`public/index.php` est l'unique point d'entrée public. Il :

1. démarre la session ;
2. charge Composer ;
3. construit le conteneur PHP-DI ;
4. récupère le dispatcher FastRoute ;
5. extrait le chemin et la méthode HTTP ;
6. traite les réponses `404`, `405` et `FOUND` ;
7. récupère le contrôleur dans le conteneur ;
8. appelle l'action correspondante.

### 4.2 Routage

Les routes sont définies dans `routes/web.php`. Les paramètres numériques sont contraints par `\\d+`.

| Méthode | URL | Action |
| --- | --- | --- |
| `GET` | `/` | Redirection vers les salles |
| `GET` | `/salles` | `SalleController::index()` |
| `GET` | `/salles/create` | `SalleController::create()` |
| `POST` | `/salles` | `SalleController::store()` |
| `GET` | `/salles/{id}` | `SalleController::show()` |
| `GET` | `/salles/{id}/edit` | `SalleController::edit()` |
| `POST` | `/salles/{id}/edit` | `SalleController::update()` |
| `GET` | `/reservations` | `ReservationController::index()` |
| `GET` | `/reservations/create` | `ReservationController::create()` |
| `POST` | `/reservations` | `ReservationController::store()` |
| `GET` | `/reservations/{id}` | `ReservationController::show()` |
| `POST` | `/reservations/{id}/cancel` | `ReservationController::cancel()` |

### 4.3 Contrôleurs

Les contrôleurs lisent les données HTTP, appellent les composants adaptés et choisissent la vue. Ils ne construisent pas les repositories et ne contiennent pas de requête ORM directe.

- `SalleController` gère les actions de création, consultation et modification des salles.
- `ReservationController` gère la consultation, création et annulation des réservations.

Les vues sont rendues par `renderView()` dans `src/View/helpers.php`. Cette fonction reçoit le nom logique de la vue et les données à exposer :

```php
renderView('reservation/show', [
    'reservation' => $reservation,
    'title' => $title,
]);
```

### 4.4 Validators

`SalleValidator` et `ReservationValidator` implémentent `ValidatorInterface`. Ils utilisent Respect/Validation pour vérifier les chaînes, longueurs, nombres, emails et valeurs autorisées.

Le résultat est un objet `ValidationResult` qui contient :

- `isValid()` : indique si les données sont valides ;
- `errors()` ou `getErrors()` : retourne les erreurs par champ ;
- `getAcceptedData()` : retourne les valeurs nettoyées et typées.

La validation syntaxique reste séparée des règles métier. Par exemple, le validateur vérifie qu'une date est correcte, tandis que le service vérifie qu'elle est future et disponible.

### 4.5 DTO

Les DTO transportent des données validées entre le contrôleur et le service :

- `SalleDTO` contient le nom, le bâtiment, la capacité, le type et l'état ;
- `ReservationDTO` contient la salle, le responsable, l'email, le motif et deux `DateTimeImmutable`.

Un DTO ne lit pas `$_POST`, ne fait pas de requête et n'appelle pas `save()`.

### 4.6 Services

`ReservationService` porte le processus de création d'une réservation : salle existante, état actif, ordre des dates, durée, futur et chevauchement.

`AnnulerReservationService` recherche une réservation, refuse une réservation inexistante ou déjà annulée, puis délègue son annulation au repository.

Les services dépendent des interfaces Repository et de `Capsule\Manager` pour la transaction. Ils ne connaissent ni FastRoute, ni les vues, ni `$_POST`.

## 5. Modèles et ORM

`Salle` et `Reservation` sont des modèles Eloquent Active Record.

Relations :

```php
$salle->reservations();
$reservation->salle();
```

- Une salle possède plusieurs réservations (`HasMany`).
- Une réservation appartient à une salle (`BelongsTo`).

Les propriétés `$fillable` protègent l'assignation de masse. Les propriétés `$casts` convertissent `active` et `capacite`, ainsi que les dates de réservation.

## 6. Repositories

Les services et contrôleurs dépendent des interfaces :

- `SalleRepositoryInterface` ;
- `ReservationRepositoryInterface`.

Les implémentations actives sont :

- `EloquentSalleRepository` ;
- `EloquentReservationRepository`.

Elles centralisent les requêtes Eloquent, les relations, la création, la modification, l'annulation et la recherche de chevauchement. PHP-DI relie les interfaces à ces implémentations dans `config/container.php`.

## 7. Injection de dépendances et PHP-DI

Le conteneur est configuré dans `config/container.php` avec :

- le gestionnaire Eloquent ;
- les implémentations des interfaces Repository ;
- le dispatcher FastRoute.

Les contrôleurs et services reçoivent leurs dépendances par constructeur. L'application limite l'appel à `$container->get()` au point d'entrée.

Cette organisation applique l'inversion de contrôle : les classes ne construisent pas elles-mêmes leurs dépendances.

## 8. Sécurité

### CSRF

`App\Security\Csrf` génère un token stocké en session. Les formulaires POST transmettent `_csrf_token`, et les contrôleurs vérifient ce token avant toute modification.

### Échappement HTML

Les valeurs dynamiques affichées dans les vues passent par :

```php
htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
```

### Configuration

`.env` est ignoré par Git. `.env.example` documente les variables nécessaires sans contenir de secret réel.

## 9. Base de données

Les migrations se trouvent dans `database/migrations/` :

1. création de `salles` ;
2. création de `reservations` avec une clé étrangère vers `salles`.

Le script `database/migrate.php` charge les migrations dans l'ordre. Les migrations vérifient l'existence des tables afin de pouvoir être relancées sans erreur.

Le script `database/seed.php` insère les salles initiales avec `updateOrInsert()`. Il peut donc être exécuté plusieurs fois sans créer de doublons pour un même nom et bâtiment.

## 10. Installation locale

Prérequis : PHP 8.2 ou supérieur, Composer, MySQL et les extensions PDO MySQL et mbstring.

```bash
cp .env.example .env
composer install
php database/migrate.php
php database/seed.php
php -S localhost:8081 -t public
```

L'application est alors accessible sur `http://localhost:8081`.

## 11. Installation Docker

```bash
cp .env.example .env
docker compose up --build
```

Le service `db` utilise MySQL 8.4. Le service `app` utilise PHP 8.2 avec Apache, active `mod_rewrite`, sert le dossier `public/` et exécute les migrations dans son entrypoint.

Commandes utiles :

```bash
docker compose ps
docker compose logs -f app
docker compose exec app php database/seed.php
docker compose down
docker compose down -v
```

L'adresse publique locale est `http://localhost:8081`.

## 12. Tests

La configuration PHPUnit se trouve dans `phpunit.xml`.

```bash
./vendor/bin/phpunit
./vendor/bin/phpunit --testdox
```

Les tests unitaires actuels vérifient notamment :

- email invalide ;
- motif trop court ;
- capacité invalide ;
- type de salle inconnu ;
- données valides et données typées.

Les règles métier importantes à compléter dans la suite de tests sont : salle inexistante, salle inactive, durée excessive, date passée, chevauchement et créneaux voisins.

## 13. Publication GitHub et Docker Hub

Le projet utilise le même identifiant de version côté Git et Docker.

```bash
git add .
git commit -m "release: nouvelle version"
git tag -a V1.0.1 -m "Release V1.0.1"
git push origin feature/Hotfix V1.0.1
```

Pour construire et publier exactement le contenu d'un tag :

```bash
./docker-publish.sh V1.0.1 blacksnakesn
```

Cette commande produit :

```text
blacksnakesn/gestion-salle-universitaire:V1.0.1
```

Le script refuse un tag Git inexistant ou un tag ne contenant pas de `Dockerfile`. La publication est manuelle et n'utilise pas GitHub Actions.

## 14. Principes SOLID appliqués

- **Responsabilité unique** : contrôleurs, validators, services, repositories et vues ont des rôles séparés.
- **Ouvert/fermé** : une autre implémentation de repository peut remplacer Eloquent via PHP-DI.
- **Substitution de Liskov** : les implémentations respectent les contrats Repository.
- **Ségrégation des interfaces** : salles et réservations ont des contrats distincts.
- **Inversion des dépendances** : les services dépendent d'interfaces et non de classes Eloquent concrètes.

## 15. Arborescence fonctionnelle

```text
config/                 Configuration PHP-DI et base de données
database/               Migrations, migration runner et seed
public/                 Front Controller, CSS et réécriture Apache
routes/                 Définition des routes FastRoute
src/Controller/         Actions HTTP
src/DTO/                Objets de transport
src/Model/              Modèles Eloquent
src/Repository/         Contrats et implémentations d'accès aux données
src/Security/           Protection CSRF
src/Service/            Règles métier
src/Validation/         Contrats et validateurs
src/View/               Fonction renderView
templates/              Vues PHP
 tests/                 Tests PHPUnit
```
