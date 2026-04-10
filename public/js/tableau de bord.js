// Clé du "tiroir" localStorage — même clé que dans l'autre script
// pour accéder aux mêmes données
const STORAGE_KEY = "promaticTickets";


/* ------------------------------------------------------------
   GESTION DU LOCALSTORAGE
   Fonctions identiques au script de création de ticket
   ------------------------------------------------------------ */

/**
 * Charge les tickets depuis le localStorage
 * @returns un tableau de tickets, ou [] si vide ou erreur
 */
function loadTickets() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);

        // Rien en localStorage → tableau vide
        if (!raw) {
            return [];
        }

        const parsed = JSON.parse(raw); // Convertit le texte JSON en tableau JS

        // Sécurité : vérifie que c'est bien un tableau
        return Array.isArray(parsed) ? parsed : [];

    } catch (error) {
        console.error("Impossible de lire les tickets:", error);
        return [];
    }
}

/**
 * Sauvegarde le tableau de tickets dans le localStorage
 * @param tickets — le tableau complet à sauvegarder
 */
function saveTickets(tickets) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(tickets));
}


/* ------------------------------------------------------------
   FONCTIONS UTILITAIRES
   ------------------------------------------------------------ */

/**
 * Normalise le type d'un ticket pour le filtre
 * Peu importe la casse ou les variantes ("Facturable", "FACTURABLE"...)
 * on ramène tout à "facturable", "inclus" ou "inconnu"
 * @param ticketType — la valeur brute du champ ticket_type
 * @returns "facturable" | "inclus" | "inconnu"
 */
function normalizeType(ticketType) {
    const value = (ticketType || "").toLowerCase(); // Tout en minuscules pour comparer sans souci de casse

    if (value.includes("facturable")) {
        return "facturable";
    }
    if (value.includes("inclus")) {
        return "inclus";
    }
    return "inconnu"; // Aucun des deux → type non reconnu
}

/**
 * Crée une ligne de tableau vide avec un message
 * Utilisée quand il n'y a aucun ticket à afficher
 * @param message — le texte à afficher (ex: "Aucun ticket pour le moment.")
 * @returns un élément <tr> avec le message centré sur toute la largeur
 */
function createEmptyRow(message) {
    const row  = document.createElement("tr");
    const cell = document.createElement("td");
    cell.colSpan   = 7;          // S'étend sur toutes les colonnes du tableau (7 colonnes)
    cell.className = "empty";    // Style CSS centré et grisé
    cell.textContent = message;
    row.appendChild(cell);
    return row;
}

/**
 * Crée une ligne <tr> complète pour un ticket
 * @param ticket — l'objet ticket à afficher
 * @param onDelete — fonction à appeler quand on clique sur "Supprimer"
 * @returns un élément <tr> prêt à être inséré dans le tableau
 */
function createTicketRow(ticket, onDelete) {
    const row = document.createElement("tr");

    // Colonne Titre
    const titleCell = document.createElement("td");
    titleCell.textContent = ticket.title || "-"; // Si pas de titre → affiche "-"

    // Colonne Statut
    const statusCell = document.createElement("td");
    statusCell.textContent = ticket.status || "-";

    // Colonne Priorité
    const priorityCell = document.createElement("td");
    priorityCell.textContent = ticket.priority || "-";

    // Colonne Type
    const typeCell = document.createElement("td");
    typeCell.textContent = ticket.ticketType || "-";

    // Colonne Temps réel
    const timeCell = document.createElement("td");
    timeCell.textContent = ticket.actualTime || "0";

    // Colonne Assignés
    // Si c'est un tableau non vide → "Alice, Bob", sinon → "-"
    const assigneesCell = document.createElement("td");
    assigneesCell.textContent = Array.isArray(ticket.assignees) && ticket.assignees.length > 0
        ? ticket.assignees.join(", ")
        : "-";

    // Colonne Actions — bouton Supprimer
    const actionsCell  = document.createElement("td");
    const deleteButton = document.createElement("button");
    deleteButton.type      = "button";
    deleteButton.textContent = "Supprimer";
    deleteButton.className = "delete-btn";

    // Au clic sur Supprimer, appelle la fonction onDelete avec l'ID du ticket
    deleteButton.addEventListener("click", function () {
        onDelete(ticket.id);
    });

    actionsCell.appendChild(deleteButton);

    // Ajoute toutes les cellules à la ligne dans l'ordre
    row.appendChild(titleCell);
    row.appendChild(statusCell);
    row.appendChild(priorityCell);
    row.appendChild(typeCell);
    row.appendChild(timeCell);
    row.appendChild(assigneesCell);
    row.appendChild(actionsCell);

    return row;
}


/* ============================================================
   FONCTION PRINCIPALE — AFFICHAGE ET GESTION DU TABLEAU
   ============================================================ */

/**
 * Initialise le tableau des tickets :
 * - Gère le filtre par type
 * - Gère la suppression
 * - Dessine le tableau
 */
function renderTickets() {
    const tbody  = document.querySelector("#tickets-body"); // Corps du tableau HTML
    const filter = document.querySelector("#type-filter");  // Menu déroulant de filtre

    // Si ces éléments n'existent pas sur la page → on arrête tout
    if (!tbody || !filter) {
        return;
    }

    let tickets = loadTickets(); // Charge tous les tickets depuis le localStorage

    /**
     * Supprime un ticket par son ID
     * Met à jour le localStorage ET redessine le tableau immédiatement
     * @param ticketId — l'ID du ticket à supprimer
     */
    function deleteTicket(ticketId) {
        // Garde tous les tickets SAUF celui avec cet ID
        tickets = tickets.filter((ticket) => ticket.id !== ticketId);
        saveTickets(tickets); // Sauvegarde la nouvelle liste sans le ticket supprimé
        draw();               // Redessine le tableau pour refléter la suppression
    }

    /**
     * Dessine (ou redessine) le tableau selon le filtre actif
     * Appelée au chargement ET à chaque changement de filtre
     */
    function draw() {
        const selectedType = filter.value; // Valeur du filtre : "all", "facturable" ou "inclus"

        // Si "all" → on garde tous les tickets
        // Sinon → on garde seulement les tickets du type sélectionné
        const visibleTickets = selectedType === "all"
            ? tickets
            : tickets.filter((ticket) => normalizeType(ticket.ticketType) === selectedType);

        tbody.innerHTML = ""; // Vide le tableau avant de le redessiner

        // Si aucun ticket à afficher → ligne de message vide
        if (visibleTickets.length === 0) {
            tbody.appendChild(createEmptyRow("Aucun ticket pour le moment."));
            return;
        }

        // Crée et ajoute une ligne pour chaque ticket visible
        visibleTickets.forEach((ticket) => {
            tbody.appendChild(createTicketRow(ticket, deleteTicket));
        });
    }

    // Quand l'utilisateur change le filtre → on redessine le tableau
    filter.addEventListener("change", draw);

    // Premier affichage au chargement de la page
    draw();
}

// Lance renderTickets() seulement quand tout le HTML est chargé
// Évite les erreurs si le script est dans le <head> avant le <body>
document.addEventListener("DOMContentLoaded", renderTickets);