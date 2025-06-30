<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'votre_base_de_donnees';
$username = 'votre_utilisateur';
$password = 'votre_mot_de_passe';

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Requête pour récupérer toutes les données
    $stmt = $pdo->query("SELECT * FROM encombrants ORDER BY nom, prenom");
    $donnees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $erreur = "Erreur de connexion : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Encombrants</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .table-responsive {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .badge-rgpd {
            font-size: 0.8em;
        }
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body class="bg-light">
    <!-- En-tête -->
    <div class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0"><i class="bi bi-table"></i> Gestion des Encombrants</h1>
                    <p class="mb-0 mt-2">Liste des demandes enregistrées</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-light text-dark fs-6">
                        <?php echo isset($donnees) ? count($donnees) : 0; ?> demande(s)
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if (isset($erreur)): ?>
            <!-- Affichage des erreurs -->
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <strong>Erreur !</strong> <?php echo htmlspecialchars($erreur); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php else: ?>
            
            <?php if (empty($donnees)): ?>
                <!-- Message si aucune donnée -->
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h3 class="text-muted mt-3">Aucune demande enregistrée</h3>
                    <p class="text-muted">Les demandes d'enlèvement d'encombrants apparaîtront ici.</p>
                </div>
            <?php else: ?>
                
                <!-- Statistiques rapides -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <i class="bi bi-people-fill text-primary fs-2"></i>
                                <h4 class="mt-2"><?php echo count($donnees); ?></h4>
                                <small class="text-muted">Total demandes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <i class="bi bi-shield-check text-success fs-2"></i>
                                <h4 class="mt-2">
                                    <?php 
                                    $rgpd_oui = array_filter($donnees, function($item) {
                                        return strtolower($item['consentement_rgpd']) === 'oui' || $item['consentement_rgpd'] == 1;
                                    });
                                    echo count($rgpd_oui);
                                    ?>
                                </h4>
                                <small class="text-muted">Consentement RGPD</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <i class="bi bi-recycle text-info fs-2"></i>
                                <h4 class="mt-2">
                                    <?php 
                                    $types = array_unique(array_column($donnees, 'type_encombrant'));
                                    echo count($types);
                                    ?>
                                </h4>
                                <small class="text-muted">Types d'encombrants</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <i class="bi bi-envelope-fill text-warning fs-2"></i>
                                <h4 class="mt-2">
                                    <?php 
                                    $emails_uniques = array_unique(array_column($donnees, 'email'));
                                    echo count($emails_uniques);
                                    ?>
                                </h4>
                                <small class="text-muted">Emails uniques</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau des données -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-ul"></i> Liste détaillée des demandes
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col"><i class="bi bi-person"></i> Nom</th>
                                        <th scope="col"><i class="bi bi-person"></i> Prénom</th>
                                        <th scope="col"><i class="bi bi-telephone"></i> Téléphone</th>
                                        <th scope="col"><i class="bi bi-envelope"></i> Email</th>
                                        <th scope="col"><i class="bi bi-geo-alt"></i> Adresse</th>
                                        <th scope="col"><i class="bi bi-trash"></i> Type Encombrant</th>
                                        <th scope="col"><i class="bi bi-shield-check"></i> RGPD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($donnees as $index => $ligne): ?>
                                    <tr>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($ligne['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($ligne['prenom']); ?></td>
                                        <td>
                                            <a href="tel:<?php echo htmlspecialchars($ligne['telephone']); ?>" 
                                               class="text-decoration-none">
                                                <i class="bi bi-telephone-fill text-success"></i>
                                                <?php echo htmlspecialchars($ligne['telephone']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($ligne['email']); ?>" 
                                               class="text-decoration-none">
                                                <i class="bi bi-envelope-fill text-primary"></i>
                                                <?php echo htmlspecialchars($ligne['email']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($ligne['adresse_postale']); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                <?php echo htmlspecialchars($ligne['type_encombrant']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            $consentement = strtolower($ligne['consentement_rgpd']);
                                            if ($consentement === 'oui' || $ligne['consentement_rgpd'] == 1): 
                                            ?>
                                                <span class="badge bg-success badge-rgpd">
                                                    <i class="bi bi-check-circle"></i> Oui
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger badge-rgpd">
                                                    <i class="bi bi-x-circle"></i> Non
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="mt-5 py-4 bg-dark text-white">
        <div class="container text-center">
            <p class="mb-0">
                <i class="bi bi-calendar"></i> 
                Dernière mise à jour : <?php echo date('d/m/Y H:i'); ?>
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>