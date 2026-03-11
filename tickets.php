<?php
require_once 'config/db.php';

// Récupérer tous les tickets depuis la BDD
$stmt = $pdo->query("
    SELECT tickets.*, projets.nom AS nom_projet, users.prenom, users.nom AS nom_user
    FROM tickets
    JOIN projets ON tickets.projet_id = projets.id
    JOIN users ON tickets.assignee_id = users.id
");
$tickets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des tickets</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .inclus { color: green; }
        .facturable { color: red; }
    </style>
</head>
<body>
    <h1>Liste des tickets</h1>

    <table>
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
        <tbody>
            <?php foreach ($tickets as $ticket) : ?>
            <tr>
                <td><?= htmlspecialchars($ticket['titre'] ?? '-') ?></td>
                <td><?= htmlspecialchars($ticket['statut'] ?? '-') ?></td>
                <td><?= htmlspecialchars($ticket['priorite'] ?? '-') ?></td>
                <td class="<?= htmlspecialchars($ticket['type'] ?? '') ?>">
                    <?= htmlspecialchars($ticket['type'] ?? '-') ?>
                </td>
                <td><?= htmlspecialchars($ticket['temps_reel'] ?? '-') ?></td>
                <td><?= htmlspecialchars(($ticket['prenom'] ?? '') . ' ' . ($ticket['nom_user'] ?? '')) ?></td>
                <td>-</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
