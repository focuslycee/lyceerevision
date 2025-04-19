<!-- download.php -->
<!DOCTYPE html>
<html lang="fr">
<head>    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Télécharger Fichiers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="container">
        <nav>
            <div class="logo">Communité des élèves</div>
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="upload.php">Uploader</a></li>
                <li><a href="download.php" class="active">Télécharger</a></li>
            </ul>
        </nav>
    </div>
</header>

<?php
include 'db_config.php';

$typeFilter = $_GET['type'] ?? '';
$niveauFilter = $_GET['niveau_scolaire'] ?? '';

$sql = "SELECT * FROM files WHERE 1=1";
$params = [];
$types = '';

if (!empty($typeFilter)) {
    $sql .= " AND type = ?";
    $params[] = $typeFilter;
    $types .= 's';
}
if (!empty($niveauFilter)) {
    $sql .= " AND niveau_scolaire = ?";
    $params[] = $niveauFilter;
    $types .= 's';
}

$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("Erreur SQL: " . $conn->error);
}
?>

<section class="container">
    <h2>Filtrer les fichiers</h2>
    <form method="GET" action="download.php">
        <label for="type">Type :</label>
        <select name="type" id="type">
            <option value="">Tous</option>
            <option value="resume" <?= $typeFilter == 'resume' ? 'selected' : '' ?>>Résumé</option>
            <option value="exercice" <?= $typeFilter == 'exercice' ? 'selected' : '' ?>>Exercice</option>
            <option value="examen" <?= $typeFilter == 'examen' ? 'selected' : '' ?>>Examen</option>
        </select>

        <label for="niveau_scolaire">Niveau Scolaire :</label>
        <select name="niveau_scolaire" id="niveau_scolaire">
            <option value="">Tous</option>
            <?php
            $niveaux = [
                "tronc_commun_sciences" => "Tronc Commun Sciences",
                "tronc_commun_technologies" => "Tronc Commun Technologies",
                "tronc_commun_lettres_sciences_humaines" => "Tronc Commun Lettres et Sciences Humaines",
                "1ere_bac_sc_experimentales" => "1ère Bac Sciences Expérimentales",
                "1ere_bac_sc_mathematiques" => "1ère Bac Sciences Mathématiques",
                "1ere_bac_economique_gestion" => "1ère Bac Economique et Gestion",
                "1ere_bac_lettres_sciences_humaines" => "1ère Bac Lettres et Sciences Humaines",
                "2eme_bac_sc_physiques" => "2ème Bac Sciences Physiques",
                "2eme_bac_sc_svt" => "2ème Bac Sciences SVT",
                "2eme_bac_sc_mathematiques_A" => "2ème Bac Sciences Mathématiques A",
                "2eme_bac_sc_mathematiques_B" => "2ème Bac Sciences Mathématiques B",
                "2eme_bac_sc_economiques" => "2ème Bac Sciences Economiques",
                "2eme_bac_sc_gestion_comptable" => "2ème Bac Sciences de Gestion Comptable",
                "2eme_bac_lettres" => "2ème Bac Lettres",
                "2eme_bac_sc_humaines" => "2ème Bac Sciences Humaines",
            ];

            foreach ($niveaux as $value => $label) {
                $selected = $niveauFilter == $value ? 'selected' : '';
                echo "<option value=\"$value\" $selected>$label</option>";
            }
            ?>
        </select>

        <button type="submit" class='btn'>Filtrer</button>
    </form>

    <h2>Fichiers disponibles</h2>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="file-item">';
            echo '<p><strong>' . htmlspecialchars($row['filename']) . '</strong></p>';
            echo '<p>Type : ' . ucfirst(htmlspecialchars($row['type'])) . '</p>';
            echo '<p>Niveau Scolaire : ' . htmlspecialchars($niveaux[$row['niveau_scolaire']] ?? $row['niveau_scolaire']) . '</p>';
            if (file_exists($row['filepath'])) {
                echo '<a href="download_file.php?file=' . urlencode($row['filepath']) . '">Télécharger</a>';
            } else {
                echo '<p>Fichier introuvable : ' . htmlspecialchars($row['filepath']) . '</p>';
            }
            echo '</div>';
        }
    } else {
        echo "<p>Aucun fichier disponible.</p>";
    }
    ?>
</section>

<footer>
    <div class="container">
        <p>&copy; 2025 Communité des élèves. Tous droits réservés.</p>
    </div>
</footer>
</body>
</html>
