<?php
require_once '../config/db.php';

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$filter = isset($_GET['type']) ? (string)$_GET['type'] : 'all';
$allowed_filters = ['all', 'inclus', 'facturable'];
if (!in_array($filter, $allowed_filters, true)) {
    $filter = 'all';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    $stmt = $pdo->prepare('DELETE FROM tickets WHERE id = :id');
    $stmt->execute([':id' => $delete_id]);

    header('Location: dashboard.php');
    exit;
}

$sql = "
    SELECT tickets.*, projets.nom AS nom_projet, users.prenom, users.nom AS nom_user
    FROM tickets
    JOIN projets ON tickets.projet_id = projets.id
    JOIN users ON tickets.assignee_id = users.id
";
$params = [];
if ($filter !== 'all') {
    $sql .= " WHERE tickets.type = :type";
    $params[':type'] = $filter;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dashboard.css">
    <title>Tableau de bord</title>
    <style>
    </style>
</head>
<body>
    <header class="top">
        <a href="../nouveau projet/NewProject.php" class="nav-btn">
            <h2>Nouveau projets</h2>
        </a>
        <h1>Promatic</h1>
        <a href="../nouveau ticket/NewTicket.php" class="nav-btn">
            <h2>Nouveau Ticket</h2>
        </a>
    </header>

    <main class="page">
        <section class="section">
            <h2>Tickets</h2>
            <p class="muted">Liste des tickets enregistr�s depuis le formulaire.</p>

            <div class="table-wrap">
                <table class="tickets-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Statut</th>
                            <th>Priorité</th>
                            <th>
                                Type
                                <form method="get" class="type-filter-form">
                                    <select id="type-filter" name="type" class="type-filter" onchange="this.form.submit()">
                                        <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>Tous</option>
                                        <option value="inclus" <?php echo $filter === 'inclus' ? 'selected' : ''; ?>>Inclus</option>
                                        <option value="facturable" <?php echo $filter === 'facturable' ? 'selected' : ''; ?>>Facturable</option>
                                    </select>
                                </form>
                            </th>
                            <th>Temps réel (h)</th>
                            <th>Collaborateurs</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tickets-body">
                        <?php if (!$tickets) : ?>
                            <tr>
                                <td colspan="7" class="empty">Aucun ticket pour le moment.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($tickets as $ticket) : ?>
                                <tr>
                                    <td><?= e($ticket['titre'] ?? '-') ?></td>
                                    <td><?= e($ticket['statut'] ?? '-') ?></td>
                                    <td><?= e($ticket['priorite'] ?? '-') ?></td>
                                    <td class="<?= e($ticket['type'] ?? '') ?>">
                                        <?= e($ticket['type'] ?? '-') ?>
                                    </td>
                                    <td><?= e($ticket['temps_reel'] ?? '-') ?></td>
                                    <td><?= e(($ticket['prenom'] ?? '') . ' ' . ($ticket['nom_user'] ?? '')) ?></td>
                                    <td>
                                        <form method="post">
                                            <input type="hidden" name="delete_id" value="<?= e($ticket['id'] ?? '') ?>">
                                            <button type="submit" class="delete-btn">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <section class="section">
            <h2>Projets</h2>
            <p class="muted">Vue d'ensemble des projets par client.</p>

            <div class="table-wrap">
                <table class="tickets-table">
                    <thead>
                        <tr>
                            <th>Projet</th>
                            <th>Client</th>
                            <th>Collaborateurs</th>
                            <th>Contrat / Heures</th>
                            <th>Tickets liés</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="empty">Aucun projet pour le moment.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
