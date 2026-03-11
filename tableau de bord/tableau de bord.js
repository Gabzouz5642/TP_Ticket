const STORAGE_KEY = "promaticTickets";

function loadTickets() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) {
            return [];
        }
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
        console.error("Impossible de lire les tickets:", error);
        return [];
    }
}

function saveTickets(tickets) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(tickets));
}

function normalizeType(ticketType) {
    const value = (ticketType || "").toLowerCase();
    if (value.includes("facturable")) {
        return "facturable";
    }
    if (value.includes("inclus")) {
        return "inclus";
    }
    return "inconnu";
}

function createEmptyRow(message) {
    const row = document.createElement("tr");
    const cell = document.createElement("td");
    cell.colSpan = 7;
    cell.className = "empty";
    cell.textContent = message;
    row.appendChild(cell);
    return row;
}

function createTicketRow(ticket, onDelete) {
    const row = document.createElement("tr");

    const titleCell = document.createElement("td");
    titleCell.textContent = ticket.title || "-";

    const statusCell = document.createElement("td");
    statusCell.textContent = ticket.status || "-";

    const priorityCell = document.createElement("td");
    priorityCell.textContent = ticket.priority || "-";

    const typeCell = document.createElement("td");
    typeCell.textContent = ticket.ticketType || "-";

    const timeCell = document.createElement("td");
    timeCell.textContent = ticket.actualTime || "0";

    const assigneesCell = document.createElement("td");
    assigneesCell.textContent = Array.isArray(ticket.assignees) && ticket.assignees.length > 0
        ? ticket.assignees.join(", ")
        : "-";

    const actionsCell = document.createElement("td");
    const deleteButton = document.createElement("button");
    deleteButton.type = "button";
    deleteButton.textContent = "Supprimer";
    deleteButton.className = "delete-btn";
    deleteButton.addEventListener("click", function () {
        onDelete(ticket.id);
    });
    actionsCell.appendChild(deleteButton);

    row.appendChild(titleCell);
    row.appendChild(statusCell);
    row.appendChild(priorityCell);
    row.appendChild(typeCell);
    row.appendChild(timeCell);
    row.appendChild(assigneesCell);
    row.appendChild(actionsCell);

    return row;
}

function renderTickets() {
    const tbody = document.querySelector("#tickets-body");
    const filter = document.querySelector("#type-filter");

    if (!tbody || !filter) {
        return;
    }

    let tickets = loadTickets();

    function deleteTicket(ticketId) {
        tickets = tickets.filter((ticket) => ticket.id !== ticketId);
        saveTickets(tickets);
        draw();
    }

    function draw() {
        const selectedType = filter.value;
        const visibleTickets = selectedType === "all"
            ? tickets
            : tickets.filter((ticket) => normalizeType(ticket.ticketType) === selectedType);

        tbody.innerHTML = "";

        if (visibleTickets.length === 0) {
            tbody.appendChild(createEmptyRow("Aucun ticket pour le moment."));
            return;
        }

        visibleTickets.forEach((ticket) => {
            tbody.appendChild(createTicketRow(ticket, deleteTicket));
        });
    }

    filter.addEventListener("change", draw);
    draw();
}

document.addEventListener("DOMContentLoaded", renderTickets);
