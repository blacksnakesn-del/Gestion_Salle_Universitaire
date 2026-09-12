# Projet de Week-end — Gestion des Réservations de Salles Universitaires

* **Niveau :** Intermédiaire  
* **Durée :** Un week-end  
* **Technologie :** PHP orienté objet (PHP 8.2+)  
* **Base de données :** MySQL  
* **Travail :** Individuel  

---

## A. Description du Projet

### 1. Contexte
L’université possède plusieurs salles utilisées pour :
* Les cours ;
* Les soutenances ;
* Les réunions ;
* Les travaux pratiques ;
* Les événements étudiants.

La réservation est actuellement réalisée par courriel. Cela provoque des doublons, car plusieurs personnes peuvent demander la même salle au même horaire.

Vous devez développer une application web permettant de consulter les salles et de gérer leurs réservations. L’application sera développée sans framework complet, mais avec des composants spécialisés installés avec **Composer**.

---

### 2. Objectifs pédagogiques
À la fin du projet, l’étudiant devra savoir :
1. Structurer une application PHP en couches ;
2. Recevoir et traiter une requête HTTP ;
3. Utiliser un routeur externe ;
4. Valider les données d’un formulaire ;
5. Manipuler une base avec un ORM ;
6. Créer des relations entre modèles ;
7. Placer les règles métier dans des services ;
8. Injecter les dépendances ;
9. Configurer un conteneur d’injection ;
10. Appliquer plusieurs principes **SOLID** ;
11. Versionner progressivement une application avec Git.

---

### 3. Dépendances imposées

Les bibliothèques suivantes doivent être utilisées :

| Besoin | Dépendance |
| :--- | :--- |
| **Routeur** | `nikic/fast-route` |
| **Validation** | `respect/validation` |
| **ORM** | `illuminate/database` |
| **Conteneur** | `php-di/php-di` |
| **Variables d’environnement** | `vlucas/phpdotenv` |

#### Description des dépendances
* **FastRoute :** Associe les méthodes et chemins HTTP à des handlers, avec gestion des paramètres dynamiques ainsi que des réponses `404` et `405`.
* **Eloquent (`illuminate/database`) :** Peut être employé en dehors de Laravel grâce à `Capsule\Manager`. Il propose un ORM Active Record, un Query Builder et un Schema Builder.
* **PHP-DI :** Peut construire automatiquement les classes à partir de leurs constructeurs et de définitions explicites. Sa documentation recommande de limiter l’accès direct au conteneur au point d’entrée de l’application.
* **Respect\Validation :** Fournit des validateurs composables pour contrôler les données d’un formulaire.

#### Installation indicative (PHP 8.2 ou 8.3)
```bash
composer require \
  nikic/fast-route \
  respect/validation:^2.4 \
  illuminate/database:^12.0 \
  php-di/php-di:^7.0 \
  vlucas/phpdotenv
```

---

### 4. Fonctionnalités attendues

#### Gestion des salles
L’utilisateur doit pouvoir :
* Consulter la liste des salles ;
* Afficher le détail d’une salle ;
* Ajouter une salle ;
* Modifier une salle ;
* Activer ou désactiver une salle.

**Structure :** `Salle(id, nom, batiment, capacite, type, active, created_at, updated_at)`  
**Types autorisés :** `cours` | `informatique` | `laboratoire` | `amphitheatre` | `reunion`

#### Gestion des réservations
L’utilisateur doit pouvoir :
* Consulter toutes les réservations ;
* Filtrer les réservations par salle ;
* Afficher une réservation ;
* Créer une réservation ;
* Annuler une réservation.

**Structure :** `Reservation(id, salle_id, responsable, email, motif, date_debut, date_fin, statut, created_at, updated_at)`  
**Statuts autorisés :** `confirmée` | `annulée`

---

### 5. Règles métier

Une réservation est acceptée uniquement si :
1. La salle existe ;
2. La salle est active ;
3. Le nom du responsable est renseigné ;
4. L’adresse électronique est valide ;
5. Le motif contient entre 5 et 255 caractères ;
6. La date de début précède la date de fin ;
7. La réservation dure au maximum quatre heures ;
8. La réservation commence dans le futur ;
9. Aucune réservation confirmée ne chevauche cette période.

> **Règle de chevauchement :**  
> Deux réservations se chevauchent lorsque :  
> `nouveauDebut < reservationExistante.dateFin` **ET** `nouvelleFin > reservationExistante.dateDebut`
>
> *Exemple :*  
> * Réservation existante : 10h00 → 12h00  
> * Nouvelle réservation : 11h00 → 13h00  
> * **Résultat : CONFLIT**

> **Remarque :** Une réservation annulée ne bloque plus la salle.

---

### 6. Contraintes architecturales

* `public/index.php` est l’unique point d’entrée ;
* Les routes sont déclarées dans un fichier séparé ;
* Les contrôleurs ne réalisent aucune requête ORM ;
* Les modèles Eloquent ne lisent pas `$_POST` ;
* Les vues ne contiennent aucune règle métier ;
* Les règles de disponibilité sont placées dans un service ;
* Les données HTTP sont validées avant leur utilisation ;
* Les dépendances sont reçues dans les constructeurs ;
* Seul le point d’entrée récupère directement un objet dans le conteneur ;
* Les mots de passe et paramètres sensibles ne sont pas versionnés.

---

### 7. Arborescence attendue

```text
reservation-salles/
├── config/
│   ├── container.php
│   └── database.php
├── database/
│   ├── migrations/
│   └── seed.php
├── public/
│   ├── assets/
│   │   └── style.css
│   └── index.php
├── routes/
│   └── web.php
├── src/
│   ├── Controller/
│   ├── DTO/
│   ├── Exception/
│   ├── Model/
│   ├── Repository/
│   ├── Service/
│   ├── Validation/
│   └── View/
├── templates/
│   ├── error/
│   ├── layout/
│   ├── reservation/
│   └── salle/
├── tests/
│   ├── Unit/
│   └── Integration/
├── .env.example
├── .gitignore
├── CHANGELOG.md
├── composer.json
├── composer.lock
├── phpunit.xml
└── README.md
```
*Il appartient aux étudiants de justifier cette organisation.*

---

### 8. Modèle de données
Il appartient aux étudiants de définir le **Diagramme de classes** et d’en déduire le **schéma de base de données**.

---

## B. Déroulement du Projet

### Étape 0 — Initialiser le dépôt
**Travail demandé :**
1. Initialisez Git.
2. Créez la branche `main`.
3. Préparez `.gitignore`.
4. Créez `README.md` et `CHANGELOG.md`.
5. Créez le premier commit.

* **Branche :** `main`
* **Tag :** `v0.0.0`

---

### Étape 1 — Initialiser le projet Composer
**Travail demandé :**
1. Créez `composer.json`.
2. Configurez l’autoloading PSR-4.
3. Installez les dépendances.
4. Créez l’arborescence.
5. Vérifiez que Composer charge une classe `App\Application`.

**Questions de réflexion :**
1. Quel est le rôle de Composer ?
2. Quelle différence existe entre `require` et `require-dev` ?
3. Pourquoi faut-il versionner `composer.lock` ?
4. Pourquoi ne versionne-t-on pas `vendor/` ?

* **Branche :** `feature/01-composer`
* **Tag :** `v0.1.0`
* **Exemples de commits :** `init: créer le projet PHP`, `chore: configurer l'autoloading`, `chore: installer les dépendances`

---

### Étape 2 — Configurer Eloquent
**Travail demandé :**
1. Ajoutez `.env.example`.
2. Chargez les variables d’environnement.
3. Configurez `Capsule\Manager`.
4. Démarrez Eloquent.
5. Vérifiez la connexion.
6. Créez les tables.

**Variables attendues dans `.env` :**
```env
APP_ENV=development
APP_DEBUG=true
DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reservation_salles
DB_USERNAME=root
DB_PASSWORD=
```

**Contraintes :**
* `.env` ne doit pas être versionné ;
* Aucune classe métier ne doit appeler `getenv()` ;
* La connexion doit être configurée une seule fois ;
* Les erreurs de connexion doivent être gérées.

**Questions de réflexion :**
1. Quel rôle joue `Capsule\Manager` ?
2. Pourquoi Eloquent peut-il fonctionner sans Laravel ?
3. Où doit se trouver le démarrage de l’ORM ?
4. Quelle différence existe entre ORM et SQL écrit à la main ?

* **Branche :** `feature/02-eloquent`
* **Tag :** `v0.2.0`

---

### Étape 3 — Créer les modèles
**Travail demandé :**
1. Créez `App\Model\Salle` et `App\Model\Reservation`.
2. Configurez :
   * Le nom des tables ;
   * Les propriétés assignables ;
   * Les conversions de types ;
   * Les dates ;
   * La relation entre les deux modèles.

**Relations attendues :**
* Une salle possède plusieurs réservations (`$salle->reservations`).
* Une réservation appartient à une salle (`$reservation->salle`).

**Questions de réflexion :**
1. Quel type de relation Eloquent avez-vous utilisé ?
2. Pourquoi déclarer `$fillable` ou `$guarded` ?
3. Pourquoi convertir `active` en booléen ?
4. Pourquoi convertir les dates en objets ?

* **Branche :** `feature/03-modeles`
* **Tag :** `v0.3.0`

---

### Étape 4 — Ajouter les données initiales
**Travail demandé :**
Créez un script permettant d’ajouter au moins cinq salles :
* **Amphithéâtre A** — 250 places
* **Salle B12** — 40 places
* **Laboratoire Chimie** — 24 places
* **Salle Informatique 1** — 30 places
* **Salle de réunion** — 12 places

> **Contrainte :** Le script doit pouvoir être exécuté plusieurs fois sans créer inutilement de doublons.

**Questions de réflexion :**
1. Quelle différence existe entre migration et seeder ?
2. Pourquoi les données initiales doivent-elles être reproductibles ?
3. Comment empêcher les doublons ?

* **Branche :** `feature/04-donnees-initiales`
* **Tag :** `v0.4.0`

---

### Étape 5 — Créer la validation
**Travail demandé :**
1. Créez un contrat commun :
```php
interface ValidatorInterface
{
    public function validate(array $data): ValidationResult;
}
```
2. Créez ensuite : `SalleValidator`, `ReservationValidator`, et `ValidationResult`.
3. Les validateurs doivent utiliser `Respect\Validation`.

`ValidationResult` devra permettre de connaître :
* Si les données sont valides ;
* Les erreurs associées à chaque champ ;
* Les données acceptées.

*Exemple d'utilisation :*
```php
$resultat = $validator->validate($data);
if (!$resultat->isValid()) {
    $errors = $resultat->errors();
}
```

**Règles de validation :**
* **Salle :**
  * `nom` : obligatoire, 2 à 100 caractères ;
  * `batiment` : obligatoire, 2 à 100 caractères ;
  * `capacite` : entier compris entre 1 et 1 000 ;
  * `type` : valeur autorisée ;
  * `active` : booléen.
* **Réservation :**
  * `salle_id` : entier positif ;
  * `responsable` : 2 à 120 caractères ;
  * `email` : adresse valide ;
  * `motif` : 5 à 255 caractères ;
  * `date_debut` : date valide ;
  * `date_fin` : date valide.

*Note : La comparaison entre les dates sera effectuée dans la couche métier.*

**Questions de réflexion :**
1. Pourquoi séparer la validation syntaxique des règles métier ?
2. Pourquoi créer une interface de validation ?
3. Pourquoi le validateur ne doit-il pas enregistrer les données ?
4. Comment retourner plusieurs erreurs en une seule fois ?

* **Branche :** `feature/05-validation`
* **Tag :** `v0.5.0`

---

### Étape 6 — Créer les objets de transport (DTO)
**Travail demandé :**
Créez `CreerSalleDTO` et `CreerReservationDTO`. Les DTO doivent contenir des données correctement typées.

*Exemple pour une réservation :*
* `salleId` : `int`
* `responsable` : `string`
* `email` : `string`
* `motif` : `string`
* `dateDebut` : `DateTimeImmutable`
* `dateFin` : `DateTimeImmutable`

> **Contrainte :** Le tableau `$_POST` ne doit pas être transmitted directement aux services.

**Questions de réflexion :**
1. Quelle différence existe entre DTO et modèle Eloquent ?
2. Pourquoi le DTO ne doit-il pas appeler `save()` ?
3. À quel moment transforme-t-on les chaînes en dates ?
4. Le DTO doit-il contenir la règle de chevauchement ?

* **Branche :** `feature/06-dto`
* **Tag :** `v0.6.0`

---

### Étape 7 — Créer l’accès aux données (Repositories)
**Travail demandé :**
1. Définissez des contrats pour les opérations nécessaires : `SalleRepositoryInterface` et `ReservationRepositoryInterface`.
2. **Opérations minimales :**
   * Lister les salles ;
   * Retrouver une salle ;
   * Enregistrer une salle ;
   * Lister les réservations ;
   * Retrouver une réservation ;
   * Rechercher un conflit ;
   * Enregistrer une réservation ;
   * Annuler une réservation.
3. Créez des implémentations basées sur Eloquent.

> **Contrainte importante :** Les contrôleurs ne doivent jamais contenir `Salle::query()`, `Reservation::where(...)`, ou `$model->save()`. Ces opérations doivent être isolées derrière les composants d’accès aux données.

**Questions de réflexion :**
1. Eloquent constitue-t-il déjà un accès aux données ?
2. Pourquoi ajouter un Repository au-dessus d’Eloquent ?
3. Cette abstraction est-elle toujours nécessaire ?
4. Quel avantage apporte-t-elle ?

* **Branche :** `feature/07-repositories`
* **Tag :** `v0.7.0`

---

### Étape 8 — Implémenter les règles métier
**Travail demandé :**
Créez :
* `CreerReservationService`
* `AnnulerReservationService`
* `SalleIndisponibleException`
* `ReservationIntrouvableException`

**Workflow du service de création (`CreerReservationService`) :**
1. Retrouver la salle ;
2. Vérifier qu’elle est active ;
3. Vérifier que le début précède la fin ;
4. Vérifier que la durée ne dépasse pas quatre heures ;
5. Vérifier que la date est future ;
6. Rechercher les chevauchements ;
7. Créer la réservation ;
8. L’enregistrer ;
9. Retourner le résultat.

> **Contrainte :** Le service ne doit connaître ni `$_POST`, ni FastRoute, ni les vues, ni le conteneur.

**Questions de réflexion :**
1. Pourquoi ces règles ne sont-elles pas dans le contrôleur ?
2. Pourquoi le service dépend-il d’une interface de Repository ?
3. Quelle exception doit être levée en cas de conflit ?
4. Comment tester le service sans MySQL ?

* **Branche :** `feature/08-services`
* **Tag :** `v0.8.0`

---

### Étape 9 — Créer les contrôleurs et les vues

#### Contrôleurs et actions minimales :
* **`SalleController` :** `index()`, `show()`, `create()`, `store()`, `edit()`, `update()`
* **`ReservationController` :** `index()`, `show()`, `create()`, `store()`, `cancel()`

#### Responsabilités de `store()` :
1. Lire les données HTTP ;
2. Appeler le validateur ;
3. Réafficher le formulaire en cas d’erreur ;
4. Construire le DTO ;
5. Appeler le service ;
6. Rediriger après succès.

#### Vues attendues :
* `templates/layout/base.php`
* `templates/salle/index.php`, `show.php`, `form.php`
* `templates/reservation/index.php`, `show.php`, `form.php`
* `templates/error/404.php`, `405.php`

**Contraintes :**
* Toutes les sorties dynamiques doivent être échappées ;
* Les vues ne doivent appeler ni Eloquent ni le conteneur ;
* Après un POST réussi, une redirection doit être effectuée (PRG) ;
* Les erreurs doivent apparaître près des champs concernés.

* **Branche :** `feature/09-interface-web`
* **Tag :** `v0.9.0`

---

### Étape 10 — Configurer FastRoute

#### Routes attendues :

| Méthode | Chemin | Action |
| :--- | :--- | :--- |
| `GET` | `/` | Accueil |
| `GET` | `/salles` | Liste des salles |
| `GET` | `/salles/create` | Formulaire d’ajout |
| `POST` | `/salles` | Enregistrer une salle |
| `GET` | `/salles/{id:\d+}` | Détail |
| `GET` | `/salles/{id:\d+}/edit` | Modification |
| `POST` | `/salles/{id:\d+}/edit` | Enregistrer la modification |
| `GET` | `/reservations` | Liste |
| `GET` | `/reservations/create` | Formulaire |
| `POST` | `/reservations` | Créer |
| `GET` | `/reservations/{id:\d+}` | Détail |
| `POST` | `/reservations/{id:\d+}/cancel` | Annuler |

**Travail demandé :**
1. Déclarez les routes dans `routes/web.php`.
2. Créez le dispatcher FastRoute.
3. Supprimez la query string avant le dispatch.
4. Gérez les résultats (`NOT_FOUND`, `METHOD_NOT_ALLOWED`, `FOUND`).
5. Pour une réponse 405, ajoutez l’en-tête `Allow`.
6. Transmettez les paramètres dynamiques à l’action.
7. Ne construisez pas les contrôleurs dans le fichier des routes.

*Format d’un handler :* `[ReservationController::class, 'show']`

**Questions de réflexion :**
1. Pourquoi FastRoute ne construit-il pas lui-même le contrôleur ?
2. Quelle différence existe entre 404 et 405 ?
3. Pourquoi contraindre `{id}` avec `\d+` ?
4. Quel composant doit interpréter le handler retourné ?

* **Branche :** `feature/10-router`
* **Tag :** `v0.10.0`

---

### Étape 11 — Configurer PHP-DI

**Travail demandé :**
Créez `config/container.php`. Le conteneur doit connaître au minimum : `Capsule\Manager`, `SalleRepositoryInterface`, `ReservationRepositoryInterface`, `SalleValidator`, `ReservationValidator`, `CreerReservationService`, `AnnulerReservationService`, `SalleController`, `ReservationController`, `FastRoute\Dispatcher`, `Application`.

*Exemple de configuration dans `config/container.php` :*
```php
use function DI\autowire;
use function DI\factory;
use function DI\get;

return [
    SalleRepositoryInterface::class => autowire(EloquentSalleRepository::class),
    ReservationRepositoryInterface::class => autowire(EloquentReservationRepository::class),
    Capsule\Manager::class => factory(function (): Capsule\Manager {
        // Configuration de la connexion
    }),
];
```

*Fichier `public/index.php` attendu :*
```php
<?php
declare(strict_types=1);

use App\Application;
use DI\ContainerBuilder;

require dirname(__DIR__) . '/vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/config/container.php');
$container = $builder->build();

$application = $container->get(Application::class);
$application->run();
```

> **Interdiction (Anti-pattern Service Locator) :**  
> Les classes ne doivent pas recevoir `ContainerInterface` pour récupérer leurs dépendances.
> 
> ❌ **A éviter :**
> ```php
> final class CreerReservationService {
>     public function __construct(private ContainerInterface $container) {}
> }
> ```
> ✔️ **Attendu :**
> ```php
> final class CreerReservationService {
>     public function __construct(
>         private SalleRepositoryInterface $salles,
>         private ReservationRepositoryInterface $reservations
>     ) {}
> }
> ```

**Questions de réflexion :**
1. Quelle différence existe entre injection et conteneur ?
2. Qu’est-ce que l’autowiring ?
3. Pourquoi les interfaces nécessitent-elles une définition ?
4. Pourquoi limiter `$container->get()` au point d’entrée ?
5. Quel anti-pattern apparaît si toutes les classes interrogent le conteneur ?

* **Branche :** `feature/11-container`
* **Tag :** `v0.11.0`

---

### Étape 12 — Ajouter les tests

1. **Tests unitaires obligatoires (Service de création) :**
   * Réservation valide ;
   * Salle inexistante ;
   * Salle inactive ;
   * Date de fin antérieure au début ;
   * Durée supérieure à quatre heures ;
   * Date passée ;
   * Conflit avec une réservation ;
   * Réservation voisine sans chevauchement.

2. **Tests de validation obligatoires :**
   * Adresse électronique invalide ;
   * Responsable vide ;
   * Capacité négative ;
   * Type de salle inconnu ;
   * Date incorrecte.

3. **Tests d’intégration minimaux :**
   * Création d’une salle avec Eloquent ;
   * Relation salle/réservations ;
   * Recherche de chevauchement ;
   * Annulation d’une réservation.

> **Contrainte :** Les tests unitaires des services ne doivent pas nécessiter MySQL. Utilisez des doublures ou des implémentations en mémoire des repositories.

* **Branche :** `feature/12-tests`
* **Tag :** `v0.12.0`

---

### Étape 13 — Finaliser l’application

**Travail demandé :**
1. Ajoutez une mise en forme CSS simple.
2. Affichez les messages de succès et d’erreur.
3. Gérez proprement les exceptions.
4. Complétez le `README.md`.
5. Complétez le `CHANGELOG.md`.
6. Produisez le diagramme de classes.
7. Vérifiez l’installation depuis un dépôt fraîchement cloné.
8. Corrigez les anomalies.

* **Branche :** `release/1.0.0`
* **Tag :** `v1.0.0`

---

## C. Organisation & Planning

### 10. Planning conseillé

```
[Samedi Matin] ──────────────────────────────────────────
 08h00 - 12h00 (4h) : Étapes 0 à 6 (Socle & Validation)
                      ► Visée : v0.6.0

[Samedi Après-midi] ─────────────────────────────────────
 13h00 - 17h00 (4h) : Étapes 7 à 9 (Repositories, Services, MVC)
                      ► Visée : v0.9.0

[Dimanche Matin] ────────────────────────────────────────
 08h00 - 11h00 (3h) : Étapes 10 & 11 (FastRoute & PHP-DI)
                      ► Visée : v0.11.0

[Dimanche Après-midi] ───────────────────────────────────
 13h00 - 16h00 (3h) : Étapes 12 & 13 (Tests, Polish & Release)
                      ► Visée : v1.0.0
```

---

### 11. Scénarios de recette

* **Scénario 1 — Réservation valide :**  
  * *Données :* Salle B12 | Début : demain 10h00 | Fin : demain 12h00 | Responsable : Awa Ndiaye (`awa.ndiaye@universite.sn`) | Motif : Cours d'architecture logicielle.  
  * *Résultat attendu :* Réservation confirmée.
* **Scénario 2 — Chevauchement :**  
  * *Données :* Existante (10h00 → 12h00), Demande (11h30 → 13h00).  
  * *Résultat attendu :* La salle est indisponible pendant cette période.
* **Scénario 3 — Réservations voisines :**  
  * *Données :* Existante (10h00 → 12h00), Demande (12h00 → 14h00).  
  * *Résultat attendu :* Réservation confirmée.
* **Scénario 4 — Salle inactive :**  
  * *Résultat attendu :* Cette salle ne peut pas être réservée.
* **Scénario 5 — Durée excessive :**  
  * *Données :* Début : 08h00 | Fin : 14h00.  
  * *Résultat attendu :* Une réservation ne peut pas dépasser quatre heures.
* **Scénario 6 — Formulaire invalide :**  
  * *Données :* Responsable vide, Email invalide, Motif "TP".  
  * *Résultat attendu :* Aucune insertion, affichage de toutes les erreurs, conservation des valeurs valides dans le formulaire.
* **Scénario 7 — URL inconnue :**  
  * `GET /inconnue` ➔ `404 — Page introuvable`.
* **Scénario 8 — Méthode non autorisée :**  
  * `DELETE /salles` ➔ `405 — Méthode non autorisée` (avec en-tête `Allow`).

---

### 12. Livrables

Le dépôt doit contenir :
1. Le code source complet ;
2. `composer.json` et `composer.lock` ;
3. `.env.example` sans secret ;
4. Les migrations ou le schéma SQL ;
5. Le script de données initiales ;
6. Les tests ;
7. `README.md` (prérequis, installation, BD, seed, serveur, tests) ;
8. `CHANGELOG.md` ;
9. Un diagramme de classes ;
10. Une courte analyse des choix architecturaux (`ARCHITECTURE.md`).

---

### 13. Travail d’analyse (`ARCHITECTURE.md`)

Dans le document `ARCHITECTURE.md`, identifiez et expliquez les notions suivantes :  
`MVC`, `Front Controller`, `Router`, `Validator`, `DTO`, `ORM`, `Active Record`, `Repository`, `Service`, `Injection par constructeur`, `Conteneur d’injection`, `Autowiring`, `Inversion de contrôle`, et les **5 principes SOLID**.

Pour chaque notion :
1. Indiquez les classes concernées ;
2. Expliquez son rôle ;
3. Donnez un avantage ;
4. Donnez une limite ou un risque ;
5. Montrez un extrait représentatif du projet.

---

### 14. Fonctionnalités Bonus

Si le cœur obligatoire est validé, vous pouvez ajouter :
* Authentification des responsables & Rôle administrateur ;
* Pagination & Recherche multicritère ;
* Tableau de bord des salles les plus utilisées ;
* Prévention CSRF & Middlewares ;
* Journalisation (Logger) ;
* API JSON & Tests HTTP ;
* Transactions lors des réservations & Protection contre les réservations simultanées ;
* Docker & CI/CD (Intégration continue).
 