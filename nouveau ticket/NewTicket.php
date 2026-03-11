<?php
require_once '../config/db.php';

function post_value(string $key): string
{
    return isset($_POST[$key]) ? trim((string)$_POST[$key]) : '';
}

function normalize_ticket_type(string $value): string
{
    $lower = strtolower($value);
    if (strpos($lower, 'facturable') !== false) {
        return 'facturable';
    }
    if (strpos($lower, 'inclus') !== false) {
        return 'inclus';
    }
    return $lower;
}

$projets = $pdo->query("SELECT * FROM projets")->fetchAll();
$users = $pdo->query("SELECT * FROM users WHERE role = 'collaborateur'")->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = post_value('title');
    $description = post_value('description');
    $status = post_value('status');
    $priority = post_value('priority');
    $ticket_type_raw = post_value('ticket_type');
    $ticket_type = normalize_ticket_type($ticket_type_raw);
    $estimated_time = post_value('estimated_time');
    $actual_time = post_value('actual_time');
    $billable_flag = post_value('billable_flag');
    $entry_date_1 = post_value('entry_date_1');
    $entry_duration_1 = post_value('entry_duration_1');
    $client_decision = post_value('client_decision');
    $client_comment = post_value('client_comment');
    $projet_id = (int)post_value('projet_id');
    $assignee_id = (int)post_value('assignee_id');

    if ($title === '') {
        $errors[] = 'Titre manquant.';
    }
    if ($description === '') {
        $errors[] = 'Description manquante.';
    }
    if ($status === '') {
        $errors[] = 'Statut manquant.';
    }
    if ($priority === '') {
        $errors[] = 'Priorite manquante.';
    }
    if ($ticket_type === '') {
        $errors[] = 'Type manquant.';
    }
    if ($actual_time === '' || !is_numeric($actual_time)) {
        $errors[] = 'Temps reel invalide.';
    }
    if ($billable_flag === '') {
        $errors[] = 'Facturation manquante.';
    }
    if ($entry_date_1 === '') {
        $errors[] = 'Date de temps manquante.';
    }
    if ($entry_duration_1 === '' || !is_numeric($entry_duration_1)) {
        $errors[] = 'Duree de temps invalide.';
    }
    if ($client_decision === '') {
        $errors[] = 'Decision client manquante.';
    }
    if ($projet_id <= 0) {
        $errors[] = 'Projet manquant.';
    }
    if ($assignee_id <= 0) {
        $errors[] = 'Collaborateur manquant.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare("
            INSERT INTO tickets (titre, description, statut, type, projet_id, createur_id, assignee_id)
            VALUES (:titre, :description, :statut, :type, :projet_id, :createur_id, :assignee_id)
        ");

        $stmt->execute([
            ':titre' => $title,
            ':description' => $description,
            ':statut' => $status,
            ':type' => $ticket_type,
            ':projet_id' => $projet_id,
            ':createur_id' => 2,
            ':assignee_id' => $assignee_id,
        ]);

        header('Location: ../tableau%20de%20bord/dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="NewTicket.css">
    <title>Nouveau ticket</title>
</head>
<body>
    <header class="top">
        <h1>Promatic</h1>
    </header>
    <main class="page">
        <header class="page-header">
            <h1>Nouveau ticket</h1>
            <p>Remplissez ce formulaire pour créer un ticket</p>
        </header>

        <form class="ticket-form" action="NewTicket.php" method="post">
            <section class="card">
                <h2>Informations principales</h2>
                <label class="field">
                    <span>Titre*</span>
                    <input type="text" name="title" placeholder="Ex. Export compta " required>
                </label>

                <label class="field">
                    <span>Description détaillée*</span>
                    <textarea name="description" rows="5" placeholder="Contexte, besoin, contraintes, livrables attendus..." required></textarea>
                </label>

                <div class="grid-3">
                    <label class="field">
                        <span>Statut*</span>
                        <select name="status" required>
                            <option value="">Sélectionner</option>
                            <option>Nouveau</option>
                            <option>En cours</option>
                            <option>Terminé</option>
                            <option>Validé</option>
                            <option>Refusé</option>
                        </select>
                    </label>

                    <label class="field">
                        <span>Priorité*</span>
                        <select name="priority" required>
                            <option value="">Aucune</option>
                            <option>Faible</option>
                            <option>Moyenne</option>
                            <option>Haute</option>
                            <option>Critique</option>
                        </select>
                    </label>

                    <label class="field">
                        <span>Type*</span>
                        <select name="ticket_type" required>
                            <option value="">Sélectionner</option>
                            <option>Inclus (contrat)</option>
                            <option>Facturable</option>
                        </select>
                    </label>
                </div>

                <div class="grid-2">
                    <label class="field">
                        <span>Projet*</span>
                        <select name="projet_id" required>
                            <option value="">Sélectionner</option>
                            <?php foreach ($projets as $projet) : ?>
                                <option value="<?= $projet['id'] ?>"><?= htmlspecialchars($projet['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="field">
                        <span>Assigné à*</span>
                        <select name="assignee_id" required>
                            <option value="">Sélectionner</option>
                            <?php foreach ($users as $user) : ?>
                                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>
            </section>

            <section class="card">
                <h2>Temps & suivi</h2>
                <div class="grid-3">
                    <label class="field">
                        <span>Temps estimé (h)</span>
                        <input type="number" name="estimated_time" min="0" step="0.25" placeholder="0.00">
                    </label>

                    <label class="field">
                        <span>Temps réel passé (h)*</span>
                        <input type="number" name="actual_time" min="0" step="0.25" placeholder="0.00" required>
                    </label>

                    <label class="field">
                        <span>Facturation*</span>
                        <select name="billable_flag" required>
                            <option value="">Sélectionner</option>
                            <option>Inclus dans le contrat</option>
                            <option>À facturer</option>
                        </select>
                    </label>
                </div>

                <div class="time-entries">
                    <h3>Entrées de temps</h3>
                    <p>Ajoutez une ou plusieurs entrées (date, durée, commentaire optionnel).</p>

                    <div class="entry-grid entry-grid--head">
                        <span>Date (1 minimum)</span>
                        <span>Durée (h)*</span>
                        <span>Commentaire</span>
                    </div>

                    <div class="entry-grid">
                        <input type="date" name="entry_date_1" required>
                        <input type="number" name="entry_duration_1" min="0" step="0.25" placeholder="1.5" required>
                        <input type="text" name="entry_note_1" placeholder="Ex: Diagnostic initial">
                    </div>

                    <div class="entry-grid">
                        <input type="date" name="entry_date_2">
                        <input type="number" name="entry_duration_2" min="0" step="0.25" placeholder="2.0">
                        <input type="text" name="entry_note_2" placeholder="Ex: Mise en place correctif">
                    </div>

                    <div class="entry-grid">
                        <input type="date" name="entry_date_3">
                        <input type="number" name="entry_duration_3" min="0" step="0.25" placeholder="0.5">
                        <input type="text" name="entry_note_3" placeholder="Ex: Retour client">
                    </div>
                </div>
            </section>

            <section class="card">
                <h2>Collaborateurs assignés</h2>
                <div class="chip-group">
                    <label class="chip"><input type="checkbox" name="assignees[]" value="Alan"> Alan</label>
                    <label class="chip"><input type="checkbox" name="assignees[]" value="Morgane"> Morgane</label>
                    <label class="chip"><input type="checkbox" name="assignees[]" value="Baptiste"> Baptiste</label>
                    <label class="chip"><input type="checkbox" name="assignees[]" value="Adam"> Adam</label>
                </div>
            </section>

            <section class="card">
                <h2>Validation client</h2>
                <p class="hint">Les tickets facturables doivent être validés avant facturation.</p>
                <div class="grid-2">
                    <label class="field">
                        <span>Décision client *</span>
                        <select name="client_decision" required>
                            <option value="">À renseigner</option>
                            <option>En attente</option>
                            <option>Accepté</option>
                            <option>Refusé</option>
                        </select>
                    </label>

                    <label class="field">
                        <span>Commentaire</span>
                        <input type="text" name="client_comment" placeholder="Motif ou précision">
                    </label>
                </div>
            </section>

            <section class="actions">
                <div id="success-banner" class="success-banner titanic" role="status" aria-live="polite">
                    Formulaire rempli avec succès
                </div>
                <div id="fail-banner" class="fail-banner <?php echo $errors ? '' : 'titanic'; ?>" role="status" aria-live="polite">
                    Veuillez remplir tout les champs obligatoire
                </div>
                <button class="btn btn-secondary" type="reset">Réinitialiser</button>
                <button class="btn btn-primary" type="submit">Créer le ticket</button>
            </section>
            
        </form>

    </main>
    <script src="nouveau ticket.js?v=2"></script>
    
</body>
</html>
