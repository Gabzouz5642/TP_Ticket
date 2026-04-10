// Clé utilisée pour stocker les tickets dans le localStorage du navigateur
// C'est comme un nom de "tiroir" dans la mémoire du navigateur
const STORAGE_KEY = "promaticTickets";


/* ------------------------------------------------------------
   FONCTIONS UTILITAIRES
   ------------------------------------------------------------ */

/**
 * Récupère la valeur d'un champ dans un formulaire
 * @param form — le formulaire dans lequel chercher
 * @param selector — le sélecteur CSS du champ (ex: 'input[name="title"]')
 * @returns la valeur du champ sans espaces, ou "" si le champ n'existe pas
 */
function getFieldValue(form, selector) {
    const field = form.querySelector(selector);
    return field ? field.value.trim() : ""; // Si le champ existe → sa valeur, sinon → ""
}

/**
 * Récupère la liste des assignés cochés dans le formulaire
 * Cherche toutes les cases à cocher "assignees" qui sont cochées
 * @returns un tableau de noms ["adam""alan"] ou [] si personne n'est coché
 */
function getAssignees(form) {
    return Array.from(form.querySelectorAll('input[name="assignees"]:checked'))
        .map((checkbox) => checkbox.value.trim()) // Récupère la valeur de chaque case cochée
        .filter(Boolean);                          // Supprime les valeurs vides éventuelles
}


/* ------------------------------------------------------------
   GESTION DU LOCALSTORAGE
   Le localStorage c'est une mini base de données dans le navigateur
   Les données persistent même après fermeture de l'onglet
   ------------------------------------------------------------ */

/**
 * Charge les tickets depuis le localStorage
 * @returns un tableau de tickets, ou [] si aucun ticket ou en cas d'erreur
 */
function loadTickets() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY); // Récupère le texte JSON stocké

        // Si rien n'est stocké, on retourne un tableau vide
        if (!raw) {
            return [];
        }

        const parsed = JSON.parse(raw); // Convertit le texte JSON en tableau JS

        // Vérifie que ce qu'on a récupéré est bien un tableau (sécurité)
        // Si c'est autre chose (ex: un objet), on retourne [] plutôt que de planter
        return Array.isArray(parsed) ? parsed : [];

    } catch (error) {
        // Si le JSON est corrompu ou illisible, on log l'erreur et on retourne []
        // plutôt que de faire planter toute la page
        console.error("Impossible de lire les tickets:", error);
        return [];
    }
}

/**
 * Sauvegarde le tableau de tickets dans le localStorage
 * JSON.stringify convertit le tableau JS en texte pour le stockage
 * @param tickets — le tableau de tickets à sauvegarder
 */
function saveTickets(tickets) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(tickets));
}


/* ------------------------------------------------------------
   GESTION DES BANNIÈRES DE NOTIFICATION
   ------------------------------------------------------------ */

/**
 * Affiche une bannière (succès ou erreur) pendant 3 secondes
 * puis la cache automatiquement
 * Rappel : .titanic = display:none (voir le CSS)
 * @param selector — ex: "#success-banner" ou "#fail-banner"
 */
function showBanner(selector) {
    const banner = document.querySelector(selector);

    // Si la bannière n'existe pas dans le HTML, on ne fait rien
    if (!banner) {
        return;
    }

    banner.classList.remove("titanic"); // Affiche la bannière (retire display:none)

    // Après 3 secondes, cache la bannière automatiquement
    setTimeout(() => {
        banner.classList.add("titanic"); // Cache la bannière (remet display:none)
    }, 3000);
}


/* ============================================================
   LOGIQUE PRINCIPALE — SOUMISSION DU FORMULAIRE
   ============================================================ */

const form = document.querySelector(".ticket-form");

// On vérifie que le formulaire existe bien sur la page avant de continuer
if (form) {

    // Désactive la validation HTML native du navigateur
    // pour gérer nous-mêmes les erreurs avec notre propre logique
    form.noValidate = true;

    form.addEventListener("submit", function (event) {
        event.preventDefault(); // Empêche le rechargement de la page (comportement par défaut d'un formulaire)


        // --- LISTE DES CHAMPS OBLIGATOIRES ---
        // Chaque entrée contient le sélecteur CSS du champ et son label (pour les messages d'erreur)
        const requiredList = [
            { selector: 'input[name="title"]',           label: "titre" },
            { selector: 'textarea[name="description"]',  label: "description" },
            { selector: 'select[name="status"]',          label: "statut" },
            { selector: 'select[name="priority"]',        label: "priorite" },
            { selector: 'select[name="ticket_type"]',     label: "type" },
            { selector: 'input[name="actual_time"]',      label: "temps reel" },
            { selector: 'select[name="billable_flag"]',   label: "facturation" },
            { selector: 'input[name="entry_date_1"]',     label: "entree 1 date" },
            { selector: 'input[name="entry_duration_1"]', label: "entree 1 duree" },
            { selector: 'select[name="client_decision"]', label: "decision client" }
        ];

        let nbErrors = 0; // Compteur d'erreurs

        // --- VALIDATION DE CHAQUE CHAMP ---
        requiredList.forEach((item) => {
            const field = form.querySelector(item.selector);
            const value = field ? field.value.trim() : "";

            if (!value) {
                // Le champ est vide → on incrémente le compteur d'erreurs
                nbErrors += 1;
                if (field) {
                    // Marque le champ comme invalide avec un message
                    // (utilisé par form.reportValidity() plus bas)
                    field.setCustomValidity("Ce champ est obligatoire.");
                }
            } else if (field) {
                // Le champ est rempli → on efface toute erreur précédente
                field.setCustomValidity("");
            }
        });

        // --- SI DES ERREURS EXISTENT → on arrête tout ---
        if (nbErrors > 0) {
            showBanner("#fail-banner");  // Affiche la bannière rouge d'erreur
            form.reportValidity();       // Affiche les messages d'erreur sur les champs invalides
            return;                      // Stop — on n'enregistre rien
        }

        // --- CONSTRUCTION DU TICKET ---
        // Tous les champs sont valides, on construit l'objet ticket
        const ticket = {
            id: Date.now(),             // ID unique basé sur le timestamp actuel (millisecondes)
            title:      getFieldValue(form, 'input[name="title"]'),
            status:     getFieldValue(form, 'select[name="status"]'),
            priority:   getFieldValue(form, 'select[name="priority"]'),
            ticketType: getFieldValue(form, 'select[name="ticket_type"]'),
            actualTime: getFieldValue(form, 'input[name="actual_time"]'),
            assignees:  getAssignees(form),          // Tableau des assignés cochés
            createdAt:  new Date().toISOString()     // Date/heure actuelle au format ISO (ex: "2024-01-15T10:30:00.000Z")
        };

        // --- SAUVEGARDE ---
        const tickets = loadTickets();  // Charge les tickets existants
        tickets.push(ticket);           // Ajoute le nouveau ticket à la liste
        saveTickets(tickets);           // Sauvegarde la liste mise à jour

        // --- FEEDBACK ET REDIRECTION ---
        showBanner("#success-banner"); // Affiche la bannière verte de succès

        // Redirige vers le dashboard après 600ms
        // (laisse le temps à l'utilisateur de voir la bannière de succès)
        setTimeout(() => {
            window.location.href = "../tableau de bord/dashboard.html";
        }, 600);
    });
}