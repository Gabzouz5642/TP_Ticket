Voici une version encore plus exhaustive de votre fichier README.md. Elle intègre des détails techniques profonds sur la structure des données, les processus métiers (logique de calcul), la sécurité et les procédures de déploiement.

🎫 TP Ticket - Système Expert de Gestion de Suivi de Temps
📖 SommairePrésentationArchitecture TechniqueModélisation des DonnéesLogique Métier & FluxInstallation & ConfigurationInterface APISécurité & StandardsContribution📖 PrésentationTP Ticket est une solution de gestion de projet "Time Tracking" conçue pour les agences et freelances. Elle permet de transformer des interventions techniques en données exploitables pour la facturation en calculant l'écart entre le temps estimé et le temps réel.🛠 Architecture TechniqueL'application suit le pattern MVC (Modèle-Vue-Contrôleur) classique de Laravel :Modèles : Situés dans app/Models/, ils gèrent l'intégrité des données et les casts de types (ex: conversion automatique des taux horaires en décimaux).Contrôleurs : Situés dans app/Http/Controllers/, ils pilotent la logique (TicketController, ProjectController).Vues : Utilisation du moteur de template Blade couplé à du CSS/JS personnalisé pour une expérience utilisateur fluide.Pile TechnologiqueFramework : Laravel 12.xFrontend : Blade, Vite, Vanilla JSBackend : PHP 8.2+Qualité : PHPUnit 11, Laravel Pint📊 Modélisation des DonnéesSchéma de Base de Données (Mermaid ERD)Extrait de codeerDiagram
    PROJECT ||--o{ TICKET : "possède"
    PROJECT {
        bigint id PK
        string name "Nom du projet"
        string client "Nom du client"
        string collaborators "Liste séparée par virgules"
        decimal hours_included "Forfait d'heures"
        decimal hourly_rate "Coût horaire"
        datetime created_at
    }
    TICKET {
        bigint id PK
        string title "Sujet de l'intervention"
        text description "Détails techniques"
        string status "Nouveau/En cours/Terminé"
        string priority "Basse/Moyenne/Haute"
        string ticket_type "Inclus/Facturable"
        decimal estimated_time "Temps prévu"
        decimal actual_time "Temps consommé"
        string billable_flag "Indicateur facturation"
        unsigned_int project_id FK "Lien projet"
        datetime created_at
    }
    USER {
        bigint id PK
        string name
        string email UK
        string password
        string remember_token
    }
Détails des Casts (Sécurité des types)Le système force la précision des données financières et temporelles via Eloquent :Heures : Casté en decimal:2 pour éviter les erreurs d'arrondi flottant.Authentification : Le mot de passe utilisateur est automatiquement haché via le cast hashed.🔄 Logique Métier & FluxProcessus de création d'un TicketLe flux suivant décrit la validation et l'enregistrement d'une intervention :Extrait de codesequenceDiagram
    participant U as Utilisateur
    participant C as TicketController
    participant M as Ticket Model
    participant DB as Base de Données

    U->>C: Soumet formulaire (titre, temps, projet)
    C->>C: Valide les données (Mass Assignment Protection)
    C->>M: Create(data)
    M->>M: Cast des types (Actual_time -> Decimal)
    M->>DB: INSERT INTO tickets
    DB-->>C: Succès (ID)
    C-->>U: Redirection Dashboard avec message
💻 Installation & Configuration1. Clonage et DépendancesBashgit clone <repository-url>
cd TP_Ticket-master
composer install
npm install
2. EnvironnementCopiez le fichier d'exemple et générez la clé de sécurité :Bashcp .env.example .env
php artisan key:generate
3. Base de donnéesPar défaut, le projet utilise SQLite. Le script de setup crée automatiquement le fichier database.sqlite.Bashphp artisan migrate --seed
4. AutomatisationLe projet inclut une commande de setup rapide définie dans composer.json :Bashcomposer run setup
🔌 Interface APIL'application propose une API RESTful documentée via les routes api.php.Point d'entréeMéthodeDescriptionParamètres requis/api/ticketsGETListe tous les ticketsAucun/api/ticketsPOSTCréation d'un tickettitle, status, actual_timeExemple de réponse JSON :JSON{
    "id": 1,
    "title": "Correction bug CSS",
    "status": "Terminé",
    "actual_time": "2.50",
    "project_id": 4
}
🛡 Sécurité & StandardsProtection Mass Assignment : Utilisation stricte de $fillable dans les modèles pour empêcher l'injection de colonnes non autorisées.CORS & CSRF : Protection activée sur toutes les routes Web pour prévenir les attaques cross-site.Hachage : Utilisation de l'algorithme Bcrypt (via hashed cast) pour les identifiants de connexion.Sécurisation API : Les données sensibles comme password et remember_token sont masquées via la propriété $hidden.

🧪 TestsLe projet utilise PHPUnit. Pour garantir l'intégrité de la logique de calcul de temps :Bashphp artisan test
Couverture actuelle : Tests unitaires des modèles et tests de fonctionnalités des routes principales.📦 Scripts Utiles (Composer)composer run dev : Lance simultanément le serveur Laravel, Vite, et le processeur de files d'attente.composer run test : Nettoie la configuration et lance la suite de tests.
