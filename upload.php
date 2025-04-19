<!-- upload.php -->
<!DOCTYPE html>
<html lang="fr">
<head>    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploader Fichiers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="container">
        <nav>
            <div class="logo">Communité des élèves</div>
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="upload.php" class="active">Uploader</a></li>
                <li><a href="download.php">Télécharger</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="container">
    <h2 class="page-title">Uploader des Fichiers</h2>
    <div class="file-section">
    <form action="upload.php" method="post" enctype="multipart/form-data">
            <!-- File Input -->
            <label for="file">Select File:</label>
            <input type="file" name="file" id="file" required accept=".pdf,.jpg,.png">

            <!-- Type Dropdown -->
            <label for="type">Type:</label>
            <select name="type" id="type" required>
                <option value="resume">Resume</option>
                <option value="exercice">Exercice</option>
                <option value="examen">Examen</option>
            </select>

            <!-- Niveau Scolaire Dropdown -->
            <label for="niveau_scolaire">Niveau Scolaire:</label>
            <select name="niveau_scolaire" id="niveau_scolaire" required>
                <option value="tronc_commun_sciences">Tronc Commun Sciences</option>
                <option value="tronc_commun_technologies">Tronc Commun Technologies</option>
                <option value="tronc_commun_lettres_sciences_humaines">Tronc Commun Lettres et Sciences Humaines</option>
                <option value="1ere_bac_sc_experimentales">1ère Bac Sciences Expérimentales</option>
                <option value="1ere_bac_sc_mathematiques">1ère Bac Sciences Mathématiques</option>
                <option value="1ere_bac_economique_gestion">1ère Bac Economique et Gestion</option>
                <option value="1ere_bac_lettres_sciences_humaines">1ère Bac Lettres et Sciences Humaines</option>
                <option value="2eme_bac_sc_physiques">2ème Bac Sciences Physiques</option>
                <option value="2eme_bac_sc_svt">2ème Bac Sciences SVT</option>
                <option value="2eme_bac_sc_mathematiques_A">2ème Bac Sciences Mathématiques A</option>
                <option value="2eme_bac_sc_mathematiques_B">2ème Bac Sciences Mathématiques B</option>
                <option value="2eme_bac_sc_economiques">2ème Bac Sciences Economiques</option>
                <option value="2eme_bac_sc_gestion_comptable">2ème Bac Sciences de Gestion Comptable</option>
                <option value="2eme_bac_lettres">2ème Bac Lettres</option>
                <option value="2eme_bac_sc_humaines">2ème Bac Sciences Humaines</option>
            </select>

            <!-- Matière Dropdown -->
            <label for="matiere">Matière:</label>
            <select name="matiere" id="matiere" required>
                <option value="mathematiques">Mathématiques</option>
                <option value="physique_chimie">Physique-Chimie</option>
                <option value="sciences_vie_terre">Sciences de la Vie et de la Terre</option>
                <option value="philosophie">Philosophie</option>
                <option value="histoire_geographie">Histoire-Géographie</option>
                <option value="economie_gestion">Economie et Gestion</option>
                <option value="arabe">Arabe</option>
                <option value="francais">Français</option>
                <option value="anglais">Anglais</option>
                <option value="espagnol">Espagnol</option>
                <!-- Add more matières as needed -->
            </select>

            <!-- Submit Button -->
            <button type="submit" name="submit" class="btn">Uploader</button>
            </form>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; 2025 communite des eleves. Tous droits réservés.</p>
    </div>
</footer>
<?php
ob_start(); // Start output buffering

// Database connection
include 'db_config.php';

if (isset($_POST['submit'])) {
    // File data
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];
    $fileError = $_FILES['file']['error'];
    $fileType = $_FILES['file']['type'];

    // Additional data from the form
    $type = $_POST['type'];              // resume, exercice, examen
    $niveau_scolaire = $_POST['niveau_scolaire'];  // grade level
    $matiere = $_POST['matiere'];        // matière

    // File path where the file will be stored
    $fileDestination = 'uploads/' . basename($fileName); // Sanitize filename

    // Check for upload errors
    if ($fileError === 0) {
        // Max file size 10MB
        if ($fileSize < 10000000) {
            // Validate file type (e.g., allow only PDF, JPG, PNG)
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            if (in_array($fileType, $allowedTypes)) {
                // Move the file to the uploads directory
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    // Use prepared statements to prevent SQL injection
                    $sql = "INSERT INTO files (filename, filepath, type, niveau_scolaire, matiere) 
                            VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("sssss", $fileName, $fileDestination, $type, $niveau_scolaire, $matiere);
                        if ($stmt->execute()) {
                            // Success: Show success message with alert and redirect
                            echo "<script>
                                    alert('Fichier uploadé avec succès !');
                                    window.location.href = 'index.php'; // Redirect automatically to index.php
                                  </script>";
                            exit(); // Stop script execution
                        } else {
                            // Error in SQL execution
                            echo "<script>
                                    alert('Erreur lors de l\'insertion dans la base de données.');
                                    window.location.href = 'error.php'; // Redirect to error page
                                  </script>";
                        }
                        $stmt->close();
                    } else {
                        // Error in preparing the SQL query
                        echo "<script>
                                alert('Erreur lors de la préparation de la requête SQL.');
                                window.location.href = 'error.php'; // Redirect to error page
                              </script>";
                    }
                } else {
                    // Error moving the uploaded file
                    echo "<script>
                            alert('Erreur lors du déplacement du fichier.');
                            window.location.href = 'error.php'; // Redirect to error page
                          </script>";
                }
            } else {
                // Invalid file type
                echo "<script>
                        alert('Type de fichier non autorisé.');
                        window.location.href = 'error.php'; // Redirect to error page
                      </script>";
            }
        } else {
            // File too large
            echo "<script>
                    alert('Le fichier est trop volumineux. Taille maximale : 10 Mo.');
                    window.location.href = 'error.php'; // Redirect to error page
                  </script>";
        }
    } else {
        // File upload error
        echo "<script>
                alert('Une erreur s\'est produite lors de l\'upload du fichier.');
                window.location.href = 'error.php'; // Redirect to error page
              </script>";
    }
}

ob_end_flush(); // Send the output buffer content
?>
</body>
</html>
