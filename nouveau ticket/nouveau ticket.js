const form = document.querySelector(".ticket-form");

function showBanner(selector) {
    const banner = document.querySelector(selector);
    if (!banner) {
        return;
    }
    banner.classList.remove("titanic");
    setTimeout(() => {
        banner.classList.add("titanic");
    }, 3000);
}

if (form) {
    form.noValidate = true;

    form.addEventListener("submit", function (event) {
        const requiredList = [
            { selector: 'input[name="title"]', label: "titre" },
            { selector: 'textarea[name="description"]', label: "description" },
            { selector: 'select[name="status"]', label: "statut" },
            { selector: 'select[name="priority"]', label: "priorite" },
            { selector: 'select[name="ticket_type"]', label: "type" },
            { selector: 'input[name="actual_time"]', label: "temps reel" },
            { selector: 'select[name="billable_flag"]', label: "facturation" },
            { selector: 'input[name="entry_date_1"]', label: "entree 1 date" },
            { selector: 'input[name="entry_duration_1"]', label: "entree 1 duree" },
            { selector: 'select[name="client_decision"]', label: "decision client" }
        ];

        let nbErrors = 0;

        requiredList.forEach((item) => {
            const field = form.querySelector(item.selector);
            const value = field ? field.value.trim() : "";

            if (!value) {
                nbErrors += 1;
                if (field) {
                    field.setCustomValidity("Ce champ est obligatoire.");
                }
            } else if (field) {
                field.setCustomValidity("");
            }
        });

        if (nbErrors > 0) {
            event.preventDefault();
            showBanner("#fail-banner");
            form.reportValidity();
        }
    });
}
