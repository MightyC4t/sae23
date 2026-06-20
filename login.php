<?php 
session_start();

if (isset($_POST['target'])) {
    $target = $_POST['target'];
} else {
    $target = isset($_GET['target']) ? $_GET['target'] : 'admin';
}

$page_title = ($target === 'admin') ? "Connexion Administration" : "Connexion Gestionnaires"; 

require "includes/header.php"; 
require "scripts/bd_query.php";

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = isset($_POST['username']) ? addslashes($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($target === 'admin') {
        $query = "SELECT * FROM Administration WHERE `login` = '$username' AND `mdp` = '$password';";
        $role_detecte = 'admin';
    } else {
        $query = "SELECT * FROM Batiment WHERE `login` = '$username' AND `mdp` = '$password';";
        $role_detecte = 'gestion';
    }

    $result = query_from_bd($query);
    
    if ($result && $result->num_rows > 0) {
        $_SESSION['connecte'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['role']     = $role_detecte;

        if ($_SESSION['role'] === 'admin') {
            header("Location: administration.php");
            exit();
        } elseif ($_SESSION['role'] === 'gestion') {
            $_SESSION['bat'] = $result->fetch_assoc()['nom'];
            header("Location: gestion.php");
            exit();
        }
    } else {
        $erreur = "Identifiants incorrects pour cet espace !";
    }
}
?>

<main class="login-container">
    <h1><?php echo ($target === 'admin') ? "Connexion administration" : "Connexion gestion de bâtiment"; ?></h1>
    
    <?php if (!empty($erreur)): ?>
        <p class="error" style="color: red; font-weight: bold;"><?php echo $erreur; ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        
        <input type="hidden" name="target" value="<?php echo htmlspecialchars($target); ?>">

        <label for="username">Identifiant :</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Se connecter</button>
    </form>
</main>

<?php require "includes/footer.php"; ?>