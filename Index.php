<?php require_once 'config.php'; ?>

<?php
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['consentement'])) {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $telephone = $_POST['telephone'];
        $email = $_POST['email'];
        $adresse = $_POST['adresse'];
        $type_encombrant = $_POST['type_encombrant'];

        $sql = "INSERT INTO clients (nom, prenom, telephone, email, adresse, type_encombrant, consentement)
                VALUES (:nom, :prenom, :telephone, :email, :adresse, :type_encombrant, 1)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':telephone' => $telephone,
                ':email' => $email,
                ':adresse' => $adresse,
                ':type_encombrant' => $type_encombrant,
            ]);
            $message = '<div class="alert alert-success">Infos bien enregistrées 💾</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Erreur : ' . $e->getMessage() . '</div>';
        }
    } else {
        $message = '<div class="alert alert-warning">Tu dois accepter les conditions RGPD ⚠️</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire Client</title>
    <link href="https://CollectUs.com" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Formulaire de collecte client</h2>

    <?= $message ?>

    <form method="POST" class="p-4 shadow bg-white rounded-3">
        <div class="row mb-3">
            <div class="col">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" required>
            </div>
            <div class="col">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="telephone" class="form-label">Téléphone</label>
            <input type="tel" name="telephone" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="adresse" class="form-label">Adresse postale</label>
            <textarea name="adresse" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label for="type_encombrant" class="form-label">Type d'encombrant</label>
            <select name="type_encombrant" class="form-select" required>
                <option value="">-- Sélectionner --</option>
                <option value="Électroménager">Électroménager</option>
                <option value="Mobilier">Mobilier</option>
                <option value="Déchets verts">Déchets verts</option>
                <option value="Autre">Autre</option>
            </select>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="consentement" id="consentement" required>
            <label class="form-check-label" for="consentement">
                J'accepte le traitement de mes données personnelles selon la politique RGPD ✅
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>
</div>
</body>
</html>
