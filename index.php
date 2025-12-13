<?php
// Configuration de la connexion à la BD (utilise le nom du service Docker Compose)
$host = 'mysql'; 
$db   = 'my_web_app_db';
$user = 'app_user';
$pass = 'app_password'; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$message = "";

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $message = "Connexion à la base de données MySQL réussie !";

    // 1. Création de la table si elle n'existe pas
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        prenom VARCHAR(255) NOT NULL
    )");

    // 2. Traitement du formulaire d'insertion
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom']) && isset($_POST['prenom'])) {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];

        $stmt = $pdo->prepare("INSERT INTO users (nom, prenom) VALUES (?, ?)");
        $stmt->execute([$nom, $prenom]);

        $message = "Information insérée avec succès pour $prenom $nom !";
    }

    // 3. Affichage des données existantes
    $stmt = $pdo->query("SELECT id, nom, prenom FROM users ORDER BY id DESC LIMIT 5");
    $users = $stmt->fetchAll();

} catch (\PDOException $e) {
    // Affiche l'erreur de connexion si le conteneur MySQL n'est pas prêt
    $message = "Erreur de connexion à la base de données : " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Site Déployé via Jenkins ok aragamiok</title>
</head>
<body>

    <h1>✅ Déploiement CI/CD Réussi</h1>
    
    <p style="color: green; font-weight: bold;"><?php echo $message; ?></p>
    
    <h2>Insertion de Données (Jenkins Goal)</h2>
    <form method="POST">
        <label for="nom">Nom :</label><br>
        <input type="text" id="nom" name="nom" required><br><br>
        <label for="prenom">Prénom :</label><br>
        <input type="text" id="prenom" name="prenom" required><br><br>
        <input type="submit" value="Insérer dans la BD">
    </form>
    
    <h2>5 Dernières Entrées dans la BD</h2>
    <?php if (!empty($users)): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr><th>ID</th><th>Nom</th><th>Prénom</th></tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['nom']); ?></td>
                    <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Aucune entrée trouvée.</p>
    <?php endif; ?>

</body>
</html>
