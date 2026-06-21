<?php
session_start();
if (!isset($_SESSION['connecte']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?target=admin");
    exit();
}

$page_title = "Administration";
$styles = ["styles/administration.css"];

require "includes/header.php";
require "scripts/db_query.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === "add_batiment") {
        $nom_bat = $_POST['nom_bat'];
        $login_bat = $_POST['login'];
        $mdp_bat = $_POST['mdp'];
        query_from_db("INSERT INTO Batiment (id_bat, nom_bat, login, mdp) VALUES (NULL, '$nom_bat', '$login_bat', '$mdp_bat');");
        $message = "Bâtiment ajouté avec succès.";
    } else if ($action === "del_batiment") {
        $nom_bat = $_POST['nom_bat'];
        query_from_db("DELETE FROM Batiment WHERE nom_bat = '$nom_bat';");
        $message = "Bâtiment supprimé.";
    } else if ($action === "add_salle") {
        $nom_salle = $_POST['nom_salle'];
        $nom_bat = $_POST['nom_bat'];
        query_from_db("
            INSERT INTO Salle (nom_salle, id_batiment) 
            SELECT '$nom_salle', Batiment.id_bat 
            FROM Batiment 
            WHERE Batiment.nom_bat = '$nom_bat';
        ");
        $message = "Salle ajoutée avec succès.";
    } else if ($action === "del_salle") {
        $nom_salle = $_POST['nom_salle'];
        query_from_db("DELETE FROM Salle WHERE nom_salle = '$nom_salle';");
        $message = "Salle supprimée.";
    } else if ($action === "add_capteur") {
        $nom_capteur = $_POST['nom_capteur'];
        $nom_salle = $_POST['nom_salle'];
        
        $type_unite = explode('|', $_POST['type_unite']);
        $type = $type_unite[0];
        $unite = $type_unite[1];
        
        query_from_db("INSERT INTO Capteur (nom_capteur, type, unite, nom_salle) VALUES ('$nom_capteur', '$type', '$unite', '$nom_salle');");
        $message = "Capteur ajouté avec succès.";
    } else if ($action === "del_capteur") {
        $nom_capteur = $_POST['nom_capteur'];
        query_from_db("DELETE FROM Capteur WHERE nom = '$nom_capteur';");
        $message = "Capteur supprimé.";
    } else if ($action === "reset_base") {
        exec('/opt/lampp/bin/mysql -u "mmoutonnet" -p"dbpassword" "sae23" -e "TRUNCATE TABLE Mesure;"');
        $message = "La base de données a été réinitialisée avec succès.";
    }
}
?>

<main id="admin-dashboard">
    <h1>Tableau de bord de supervision</h1>
    <a style="float: right;" href="logout.php">Se déconnecter</a>

    <section>
        <p>Bienvenue, <strong><?php echo $_SESSION['username']; ?></strong></p>

        <?php if (!empty($message)): ?>
            <p style="color: green; font-weight: bold;"><?php echo $message; ?></p>
        <?php endif; ?>

        <article>
            <h2>État des scripts</h2>
            <p>Dernier traitement exécuté par <code>orchestrator.sh</code> :
                <?php
                $fichier_log = "scripts/last_run.txt";
                if (file_exists($fichier_log)) {
                    echo file_get_contents($fichier_log);
                } else {
                    echo "Aucune donnée disponible (le script ne s'est jamais lancé).";
                }
                ?>
            </p>
        </article>

        <article>
            <h2>Maintenance de la base</h2>
            <form method="POST" action="" id="reset-form">
                <input type="hidden" name="action" value="reset_base">
                <button type="button" id="btn-reset" onclick="confirmerReset()" style="background-color: #ff4d4d; color: white; padding: 8px 15px; border: none; cursor: pointer; font-weight: bold;">
                    Réinitialiser la base de données
                </button>
            </form>

            <script>
                let clicCount = 0;
                function confirmerReset() {
                    clicCount++;
                    const btn = document.getElementById('btn-reset');
                    
                    if (clicCount === 1) {
                        btn.innerText = "⚠️ Cliquez à nouveau pour confirmer !";
                        btn.style.backgroundColor = "#cc0000";
                        
                        setTimeout(() => {
                            clicCount = 0;
                            btn.innerText = "Réinitialiser la base de données";
                            btn.style.backgroundColor = "#ff4d4d";
                        }, 5000);
                    } else if (clicCount === 2) {
                        document.getElementById('reset-form').submit();
                    }
                }
            </script>
        </article>

        <article>
            <h2>Gestion des bâtiments</h2>

            <?php
            $result = query_from_db("SELECT nom_bat FROM Batiment ORDER BY nom_bat ASC")->fetch_all(MYSQL_ASSOC);

            $liste_batiments = implode(' - ', array_column($result, 'nom_bat'));

            echo "Bâtiments existants : " . $liste_batiments;
            ?>

            <form method="POST" action="">
                <input type="hidden" name="action" value="add_batiment">
                <h3>Ajouter un bâtiment</h3>
                <input type="text" name="nom_bat" placeholder="Nom (ex: BatC)" required>
                <input type="text" name="login" placeholder="Login gestionnaire" required>
                <input type="text" name="mdp" placeholder="Mdp gestionnaire" required>
                <button type="submit">Ajouter</button>
            </form>

            <form method="POST" action="">
                <input type="hidden" name="action" value="del_batiment">
                <h3>Supprimer un bâtiment</h3>
                <input type="text" name="nom_bat" placeholder="Nom bâtiment à supprimer" required>
                <button type="submit" class="delete-btn">Supprimer</button>
            </form>
        </article>

        <article>
            <h2>Gestion des salles</h2>

            <?php
            $result = query_from_db("SELECT nom_salle, nom_bat FROM Batiment JOIN Salle ON Salle.id_batiment = Batiment.id_bat ORDER BY nom_salle ASC")->fetch_all(MYSQL_ASSOC);

            $liste_salles_formattee = [];
            foreach ($result as $row) {
                $liste_salles_formattee[] = $row['nom_salle'] . ' (' . $row['nom_bat'] . ')';
            }

            $liste_salles = implode(' - ', $liste_salles_formattee);

            echo "Salles existantes : " . $liste_salles;
            ?>

            <form method="POST" action="">
                <input type="hidden" name="action" value="add_salle">
                <h3>Ajouter une salle</h3>
                <input type="text" name="nom_salle" placeholder="Nom de salle (ex: E105)" required>

                <label for="nom_bat">Bâtiment parent :</label>
                <select name="nom_bat" id="nom_bat" required>
                    <option value="">-- Choisir un bâtiment --</option>
                    <?php
                    $result_batiments = query_from_db("SELECT nom_bat FROM Batiment ORDER BY nom_bat ASC")->fetch_all(MYSQL_ASSOC);
                    foreach ($result_batiments as $bat) {
                        echo '<option value="' . $bat['nom_bat'] . '">' . $bat['nom_bat'] . '</option>';
                    }
                    ?>
                </select>

                <button type="submit">Ajouter</button>
            </form>

            <form method="POST" action="">
                <input type="hidden" name="action" value="del_salle">
                <h3>Supprimer une salle</h3>
                <input type="text" name="nom_salle" placeholder="Nom de salle à supprimer" required>
                <button type="submit" class="delete-btn">Supprimer</button>
            </form>
        </article>

        <article>
            <h2>Gestion des capteurs</h2>

            <form method="POST" action="">
                <input type="hidden" name="action" value="voir_capteurs">
                <label for="salle_select">Sélectionner une salle :</label>
                <select name="salle_select" id="salle_select" onchange="this.form.submit()">
                    <option value="">-- Choisir une salle --</option>
                    <?php
                    $salles_bd = query_from_db("SELECT nom_salle FROM Salle ORDER BY nom_salle ASC")->fetch_all(MYSQL_ASSOC);
                    $salle_choisie = isset($_POST['salle_select']) ? $_POST['salle_select'] : '';

                    foreach ($salles_bd as $salle) {
                        $selected = ($salle_choisie === $salle['nom_salle']) ? 'selected' : '';
                        echo '<option value="' . $salle['nom_salle'] . '" ' . $selected . '>' . $salle['nom_salle'] . '</option>';
                    }
                    ?>
                </select>
            </form>

            <?php
            // Affichage des capteurs si une salle a été sélectionnée
            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === "voir_capteurs" && !empty($salle_choisie)) {

                $result_capteurs = query_from_db("SELECT nom_capteur FROM Capteur WHERE nom_salle = '$salle_choisie' ORDER BY nom_capteur ASC")->fetch_all(MYSQL_ASSOC);

                if (!empty($result_capteurs)) {
                    $liste_capteurs = implode(' - ', array_column($result_capteurs, 'nom_capteur'));
                    echo "<p>Capteurs existants dans la salle " . $salle_choisie . " : " . $liste_capteurs . "</p>";
                } else {
                    echo "<p>Aucun capteur trouvé dans la salle " . $salle_choisie . ".</p>";
                }
            }
            ?>

            <form method="POST" action="">
                <input type="hidden" name="action" value="add_capteur">
                <h3>Ajouter un capteur</h3>

                <input type="text" name="nom_capteur" placeholder="Nom unique (ex: Temp_E105)" required>

                <label for="type_unite">Type de capteur :</label>
                <select name="type_unite" id="type_unite" required>
                    <option value="">-- Choisir un type --</option>
                    <option value="temperature|celcius">Température (°C)</option>
                    <option value="co2|ppm">CO2 (ppm)</option>
                    <option value="humidite|pourcentage">Humidité (%)</option>
                    <option value="pressure|hPa">Pression (hPa)</option>
                    <option value="tvoc|ppb">Indice TVOC (ppb)</option>
                </select>

                <label for="nom_salle">Salle parente :</label>
                <select name="nom_salle" id="nom_salle" required>
                    <option value="">-- Choisir une salle --</option>
                    <?php
                    $salles_bd = query_from_db("SELECT nom_salle FROM Salle ORDER BY nom_salle ASC")->fetch_all(MYSQL_ASSOC);
                    foreach ($salles_bd as $s) {
                        echo '<option value="' . $s['nom_salle'] . '">' . $s['nom_salle'] . '</option>';
                    }
                    ?>
                </select>

                <button type="submit">Ajouter</button>
            </form>

            <form method="POST" action="">
                <input type="hidden" name="action" value="del_capteur">
                <h3>Supprimer un capteur</h3>
                <input type="text" name="nom_capteur" placeholder="Nom du capteur à supprimer" required>
                <button type="submit" class="delete-btn">Supprimer</button>
            </form>
        </article>
    </section>
</main>

<?php require "includes/footer.php"; ?>