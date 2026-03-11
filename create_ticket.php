<?php
require_once 'config/db.php';

// Récupérer les projets pour le formulaire
$projets = $pdo->query("SELECT * FROM projets")->fetchAll();
$users = $pdo->query("SELECT * FROM users WHERE role = 'collaborateur'")->fetchAll();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = htmlspecialchars($_POST['titre']);
    $description = htmlspecialchars($_POST['description']);
    $statut = $_POST['statut'];
    $type = $_POST['type'];
    $projet_id = $_POST['projet_id'];
    $assignee_id = $_POST['assignee_id'];

    $stmt = $pdo->prepare("
        INSERT INTO tickets (titre, description, statut, type, projet_id, createur_id, assignee_id)
        VALUES (:titre, :description, :statut, :type, :projet_id, :createur_id, :assignee_id)
    ");

    $stmt->execute([
        ':titre' => $titre,
        ':description' => $description,
        ':statut' => $statut,
        ':type' => $type,
        ':projet_id' => $projet_id,
        ':createur_id' => 2, // Sophie (collaborateur de test)
        ':assignee_id' => $assignee_id
    ]);

    header('Location: tickets.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un ticket</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { max-width: 600px; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Créer un ticket</h1>

    <form method="POST">
        <label>Titre</label>
        <input type="text" name="titre" required>

        <label>Description</label>
        <textarea name="description" rows="4"></textarea>

        <label>Statut</label>
        <select name="statut">
            <option value="nouveau">Nouveau</option>
            <option value="en_cours">En cours</option>
            <option value="en_attente_client">En attente client</option>
        </select>

        <label>Type</label>
        <select name="type">
            <option value="inclus">Inclus</option>
            <option value="facturable">Facturable</option>
        </select>

        <label>Projet</label>
        <select name="projet_id">
            <?php foreach ($projets as $projet) : ?>
                <option value="<?= $projet['id'] ?>"><?= htmlspecialchars($projet['nom']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Assigné à</label>
        <select name="assignee_id">
            <?php foreach ($users as $user) : ?>
                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Créer le ticket</button>
    </form>

    <br>
    <a href="tickets.php">← Retour à la liste</a>

</body>
</html>