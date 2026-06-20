<?php
session_start();
if (!isset($_SESSION['connecte']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?target=admin");
    exit();
}

$page_title = "Administration";
$styles = ["styles/administration.css"];

require "includes/header.php";
require "scripts/bd_query.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === "add_batiment") {
        $id_bat = addslashes($_POST['id_bat']);
        $nom_bat = addslashes($_POST['nom_bat']);
        $login_bat = addslashes($_POST['login']);
        $mdp_bat = addslashes($_POST['mdp']);
        query_from_bd("INSERT INTO Batiment (id_bat, nom, login, mdp) VALUES ('$id_bat', '$nom_bat', '$login_bat', '$mdp_bat');");
        $message = "Bâtiment ajouté avec succès.";
    }
    elseif ($action === "del_batiment") {
        $id_bat = addslashes($_POST['id_bat']);
        query_from_bd("DELETE FROM Batiment WHERE id_bat = '$id_bat';");
        $message = "Bâtiment supprimé.";
    }

    elseif ($action === "add_salle") {
        $nom_salle = $_POST['nom_salle'];
        $id_batiment = addslashes($_POST['id_batiment']);
        query_from_bd("INSERT INTO Salle (nom_salle, id_batiment) VALUES ('$nom_salle', '$id_batiment');");
        $message = "Salle ajoutée avec succès.";
    }
    elseif ($action === "del_salle") {
        $nom_salle = $_POST['nom_salle'];
        query_from_bd("DELETE FROM Salle WHERE nom_salle = '$nom_salle';");
        $message = "Salle supprimée.";
    }

    elseif ($action === "add_capteur") {
        $nom_capteur = addslashes($_POST['nom_capteur']);
        $type = $_POST['type'];
        $unite = $_POST['unite'];
        $nom_salle = $_POST['nom_salle'];
        query_from_bd("INSERT INTO Capteur (nom, type, unite, nom_salle) VALUES ('$nom_capteur', '$type', '$unite', '$nom_salle');");
        $message = "Capteur ajouté avec succès.";
    }
    elseif ($action === "del_capteur") {
        $nom_capteur = addslashes($_POST['nom_capteur']);
        query_from_bd("DELETE FROM Capteur WHERE nom = '$nom_capteur';");
        $message = "Capteur supprimé.";
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
            <h2>Gestion des bâtiments</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add_batiment">
                <h3>Ajouter un bâtiment</h3>
                <input type="text" name="id_bat" placeholder="ID Bâtiment (ex: B)" required>
                <input type="text" name="nom_bat" placeholder="Nom (ex: Informatique)" required>
                <input type="text" name="login" placeholder="Login gestionnaire" required>
                <input type="text" name="mdp" placeholder="Mdp gestionnaire" required>
                <button type="submit">Ajouter</button>
            </form>

            <form method="POST" action="">
                <input type="hidden" name="action" value="del_batiment">
                <h3>Supprimer un bâtiment</h3>
                <input type="text" name="id_bat" placeholder="ID Bâtiment à supprimer" required>
                <button type="submit" class="delete-btn">Supprimer</button>
            </form>
        </article>

        <article>
            <h2>Gestion des salles</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add_salle">
                <h3>Ajouter une salle</h3>
                <input type="text" name="nom_salle" placeholder="Nom de salle (ex: E105)" required>
                <input type="text" name="id_batiment" placeholder="ID Bâtiment parent (ex: B)" required>
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
                <input type="hidden" name="action" value="add_capteur">
                <h3>Ajouter un capteur</h3>
                <input type="text" name="nom_capteur" placeholder="Nom unique (ex: Temp_E105)" required>
                <input type="text" name="type" placeholder="Type (temperature, co2...)" required>
                <input type="text" name="unite" placeholder="Unité (celcius, ppm...)" required>
                <input type="text" name="nom_salle" placeholder="Salle parente (ex: E105)" required>
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