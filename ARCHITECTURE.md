# Architecture

## Vue d'ensemble

L'application suit une architecture MVC légère, sans framework complet.
`public/index.php` est le Front Controller : il initialise la session, construit le conteneur PHP-DI, obtient le dispatcher FastRoute et transmet la requête au contrôleur correspondant.

```text
Requête HTTP
    -> public/index.php
    -> FastRoute
    -> Controller
    -> Validator + DTO
    -> Service métier
    -> Repository
    -> Eloquent / MySQL
    -> View
```

## Composants

| Notion | Classes du projet | Rôle | Avantage | Limite |
| --- | --- | --- | --- | --- |
| MVC | `Controller`, `Model`, `templates` | Séparer traitement, données et présentation | Code organisé | Demande des contrats clairs |
| Front Controller | `public/index.php` | Point d'entrée HTTP unique | Configuration centralisée | Peut grossir s'il contient la logique métier |
| Router | `routes/web.php`, FastRoute | Associer méthode et URL à une action | Routes explicites et paramètres contraints | Ne gère pas seul l'autorisation |
| Validator | `SalleValidator`, `ReservationValidator` | Vérifier la forme des données HTTP | Retourne plusieurs erreurs | Ne remplace pas les règles métier |
| DTO | `SalleDTO`, `ReservationDTO` | Transporter des données typées | Évite de transmettre `$_POST` au service | Ajoute des objets à maintenir |
| ORM | Eloquent et `Capsule\Manager` | Traduire objets et requêtes SQL | Relations et Query Builder | Peut masquer le coût des requêtes |
| Active Record | `Salle`, `Reservation` | Modèles qui portent leur persistance | Simple pour ce projet | Couplage modèle/base de données |
| Repository | Interfaces et `Eloquent*Repository` | Isoler l'accès aux données | Services testables et remplaçables | Abstraction supplémentaire |
| Service | `ReservationService`, `AnnulerReservationService` | Porter les règles métier | Contrôleurs minces | Peut devenir un objet trop général |
| Injection constructeur | Constructeurs des contrôleurs/services | Recevoir les dépendances nécessaires | Objets explicites et testables | Constructeurs parfois longs |
| Conteneur | `config/container.php`, PHP-DI | Construire et relier les objets | Autowiring et configuration unique | Mauvaise configuration détectée à l'exécution |
| Autowiring | PHP-DI | Résoudre les classes concrètes automatiquement | Réduit le code de câblage | Les interfaces nécessitent des définitions |
| Inversion de contrôle | PHP-DI et interfaces Repository | Déléguer la construction au conteneur | Réduit le couplage | Rend le démarrage moins visible |

## SOLID

### S - Responsabilité unique

`ReservationValidator` valide les données, `ReservationService` applique les règles de disponibilité et `EloquentReservationRepository` persiste les réservations. Une modification de validation ne demande donc pas de modifier le contrôleur.

### O - Ouvert/fermé

Les services dépendent de `ReservationRepositoryInterface` et `SalleRepositoryInterface`. Une nouvelle implémentation de stockage peut être ajoutée sans modifier le service.

### L - Substitution de Liskov

Toute implémentation d'un contrat Repository doit respecter les mêmes retours et effets attendus que l'interface. Les tests unitaires peuvent ainsi fournir un double de repository.

### I - Ségrégation des interfaces

Les contrats sont séparés par responsabilité : les opérations de salles sont dans `SalleRepositoryInterface`, celles des réservations dans `ReservationRepositoryInterface`. Les contrôleurs ne dépendent pas d'un contrat général inutile.

### D - Inversion des dépendances

Les contrôleurs et services dépendent des interfaces Repository, et non d'Eloquent directement. PHP-DI relie ensuite les interfaces aux implémentations `Eloquent*Repository` dans `config/container.php`.

## Flux d'une réservation

1. FastRoute trouve `ReservationController::store`.
2. Le contrôleur lit `$_POST` et appelle `ReservationValidator`.
3. Les données valides sont transformées en `ReservationDTO`.
4. `ReservationService` vérifie la salle, l'état actif, les dates, la durée et les chevauchements.
5. `ReservationRepositoryInterface` crée la réservation.
6. Le contrôleur applique le pattern PRG avec une redirection.

Le verrouillage de la salle est encapsulé dans `EloquentSalleRepository::findForUpdate`, et la transaction est fournie au service par `Capsule\Manager`.

## Sécurité et configuration

- `.env` est ignoré par Git ; `.env.example` ne contient aucun secret.
- Les formulaires POST utilisent un token CSRF.
- Les sorties dynamiques des vues sont échappées avec `htmlspecialchars`.
- Les dépendances sont construites dans le point d'entrée via PHP-DI.
- Les migrations sont exécutées par l'entrypoint Docker et sont idempotentes.
