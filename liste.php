<?php
// Configuration de la base de données
$host = '10.15.3.2';
$dbname = 'collectus';
$username = 'collectus_user';
$password = 'SecurePass2025!';

// Variables pour les messages
$error_message = '';
$demandes = [];

try {
    // Connexion PDO à la base de données MySQL
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Requête pour récupérer toutes les demandes
    $stmt = $pdo->prepare("SELECT id, nom, prenom, telephone, email, adresse, type_encombrant, consentement_rgpd, date_soumission FROM demandes ORDER BY date_soumission DESC");
    $stmt->execute();
    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error_message = "Erreur de connexion à la base de données : " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collectus - Liste des demandes</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .header-bg {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .table th {
            background-color: #e9ecef;
            border-top: none;
        }
        .badge-oui {
            background-color: #28a745;
        }
        .badge-non {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header-bg text-white py-4 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="h2 mb-0">
                        <i class="bi bi-recycle me-2"></i>
                        Collectus - Gestion des encombrants
                    </h1>
                    <p class="mb-0 opacity-75">Liste des demandes de collecte</p>
                </div>
                <div class="col-auto">
                    <a href="index.php" class="btn btn-light">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nouvelle demande
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Messages d'erreur -->
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-primary">
                            <i class="bi bi-collection"></i>
                        </h5>
                        <h3 class="text-primary"><?php echo count($demandes); ?></h3>
                        <p class="card-text text-muted">Demandes totales</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <i class="bi bi-check-circle"></i>
                        </h5>
                        <h3 class="text-success">
                            <?php echo count(array_filter($demandes, function($d) { return $d['consentement_rgpd'] == 1; })); ?>
                        </h3>
                        <p class="card-text text-muted">Avec consentement</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-info">
                            <i class="bi bi-calendar-week"></i>
                        </h5>
                        <h3 class="text-info">
                            <?php 
                            $today = date('Y-m-d');
                            echo count(array_filter($demandes, function($d) use ($today) { 
                                return date('Y-m-d', strtotime($d['date_soumission'])) == $today; 
                            })); 
                            ?>
                        </h3>
                        <p class="card-text text-muted">Aujourd'hui</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-warning">
                            <i class="bi bi-truck"></i>
                        </h5>
                        <h3 class="text-warning">
                            <?php 
                            $types = array_count_values(array_column($demandes, 'type_encombrant'));
                            echo !empty($types) ? max($types) : 0;
                            ?>
                        </h3>
                        <p class="card-text text-muted">Type le plus demandé</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des demandes -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-table me-2"></i>
                    Liste des demandes de collecte
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($demandes)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h4 class="text-muted mt-3">Aucune demande enregistrée</h4>
                        <p class="text-muted">Les demandes de collecte apparaîtront ici.</p>
                        <a href="index.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>
                            Créer une demande
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Nom</th>
                                    <th scope="col">Prénom</th>
                                    <th scope="col">Téléphone</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Adresse</th>
                                    <th scope="col">Type d'encombrant</th>
                                    <th scope="col">Consentement RGPD</th>
                                    <th scope="col">Date de soumission</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($demandes as $demande): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($demande['id']); ?></span>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($demande['nom']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($demande['prenom']); ?></td>
                                        <td>
                                            <a href="tel:<?php echo htmlspecialchars($demande['telephone']); ?>" class="text-decoration-none">
                                                <i class="bi bi-telephone me-1"></i>
                                                <?php echo htmlspecialchars($demande['telephone']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($demande['email']); ?>" class="text-decoration-none">
                                                <i class="bi bi-envelope me-1"></i>
                                                <?php echo htmlspecialchars($demande['email']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <small><?php echo htmlspecialchars($demande['adresse']); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo htmlspecialchars($demande['type_encombrant']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($demande['consentement_rgpd']): ?>
                                                <span class="badge badge-oui">
                                                    <i class="bi bi-check-circle me-1"></i>Oui
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-non">
                                                    <i class="bi bi-x-circle me-1"></i>Non
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('d/m/Y H:i', strtotime($demande['date_soumission'])); ?>
                                            </small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-5 py-4 border-top">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        <i class="bi bi-shield-check me-1"></i>
                        Données protégées par le RGPD
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">
                        <i class="bi bi-building me-1"></i>
                        Collectus - Gestion des encombrants
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>