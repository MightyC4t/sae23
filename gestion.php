<?php
session_start();

// Ensure user is logged in and has the correct role, else we'll send it to the login page
if (!isset($_SESSION['connecte']) || $_SESSION['role'] !== 'gestion') {
    header("Location: login.php?target=gestion");
    exit();
}

$page_title = "Gestion"; 
$styles = ["styles/administration.css"];

require "includes/header.php";
require "./scripts/db_query.php";
?>

<main id="admin-dashboard">
    <h1>Tableau de bord de supervision du bâtiment <?php echo $_SESSION['bat']; ?></h1>
    <a href="logout.php">Se déconnecter</a>

    <p>Bienvenue, <strong><?php echo $_SESSION['username']; ?></strong></p>

<?php
$login_gestionnaire = $_SESSION['username'];

// Fetch all rooms assigned to the connected building manager
$salles_result = query_from_db("
	SELECT Salle.nom_salle 
	FROM Salle 
	JOIN Batiment ON Salle.id_batiment = Batiment.id_bat
	WHERE Batiment.login = '$login_gestionnaire';
");

$salles_rows = $salles_result->fetch_all(MYSQLI_ASSOC);

// We are keeping the rooms possessed by the manager
$liste_salles = [];
foreach ($salles_rows as $s_row) {
	$liste_salles[] = $s_row['nom_salle'];
}
// Here $liste_salles can look like ["E106", "E208", ...] with every room put in the database

// Block access if no rooms are assigned to this account
if (empty($liste_salles)) {
	echo "<p class='no-data'>Aucun bâtiment ou salle assigné à votre compte.</p>";
	echo '</main>';
	require "includes/footer.php";
	exit();
}

// Format room list for SQL IN ("E106","E208")
// The implode() function operates such as .join() in Python 
$salles_in_clause = '"' . implode('","', $liste_salles) . '"';

// Retrieve and sanitize GET filters and sorting inputs
$room 		 = isset($_GET['room']) 		? $_GET['room'] 		: 'all';
$filter_type = isset($_GET['filter_type']) 	? $_GET['filter_type'] 	: 'all';
$sort_by     = isset($_GET['sort_by'])     	? $_GET['sort_by']     	: 'date';
$order       = isset($_GET['order'])        ? $_GET['order']		: 'DESC';

if ($order !== 'ASC' && $order !== 'DESC') {
	$order = 'DESC';
}

// Build dynamic WHERE clause based on room selection
if ($room !== 'all' && in_array($room, $liste_salles)) {
	$where_clause = "WHERE Capteur.nom_salle = \"$room\"";
} else {
	$where_clause = "WHERE Capteur.nom_salle IN ($salles_in_clause)";
}

// Append sensor type filtering if requested
if ($filter_type !== 'all' && $filter_type !== '') {
	$where_clause .= " AND Capteur.type = \"$filter_type\"";
}

// Build dynamic ORDER BY clause with a stable index key (id_mes) to solve the CURTIME() issue
if ($sort_by === 'valeur') {
	$order_clause = "ORDER BY Mesure.valeur $order";
} else {
	$order_clause = "ORDER BY Mesure.date $order, Mesure.horaire $order";
}
$order_clause .= ", Mesure.id_mes ASC";

// Full query request
$result = query_from_db("
	SELECT Capteur.nom_salle AS Salle, Capteur.type, Mesure.date, Mesure.horaire, Mesure.valeur, Capteur.unite
	FROM Mesure 
	JOIN Capteur ON Mesure.nom_capteur = Capteur.nom_capteur 
	$where_clause
	$order_clause
	LIMIT 10;
");

$rows = $result->fetch_all(MYSQLI_ASSOC);

// Query statistics grouped by room and type for the assigned building
$stats_result = query_from_db("
	SELECT Capteur.nom_salle AS Salle, Capteur.type, ROUND(AVG(Mesure.valeur), 1) AS Moyenne, MIN(Mesure.valeur) AS Min, MAX(Mesure.valeur) AS Max, Capteur.unite
	FROM Mesure
	JOIN Capteur ON Mesure.nom_capteur = Capteur.nom_capteur
	WHERE Capteur.nom_salle IN ($salles_in_clause)
	GROUP BY Capteur.nom_salle, Capteur.type
	ORDER BY Capteur.nom_salle ASC, Capteur.type ASC;
");

$stats_rows = $stats_result->fetch_all(MYSQLI_ASSOC);

// Dictionaries for system-to-human translation
$traductionTypes = ['tvoc' => 'Taux de COV', 'pressure' => 'Pression', 'co2' => 'CO2', 'temperature' => 'Température', 'humidity' => 'Humidité'];
$traductionUnites = ['pourcentage' => '%', 'celcius' => '°C', 'ppm' => 'ppm', 'ppb' => 'ppb', 'hPa' => 'hPa'];
?>

	<section id="statistiques">
		<h2>Statistiques globales de votre bâtiment</h2>
		<?php if (!empty($stats_rows)): ?>
			<table id="tab_stats">
				<thead>
					<tr>
						<th>Salle</th>
						<th>Type de capteur</th>
						<th>Moyenne</th>
						<th>Minimum</th>
						<th>Maximum</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($stats_rows as $st_row): 
						$typeAffichage = $traductionTypes[$st_row['type']];
						$uniteAffichage = $traductionUnites[$st_row['unite']];
					?>
						<tr>
							<td><?php echo $st_row['Salle']; ?></td>
							<td><?php echo $typeAffichage; ?></td>
							<td><?php echo $st_row['Moyenne'] . ' ' . $uniteAffichage; ?></td>
							<td><?php echo $st_row['Min'] . ' ' . $uniteAffichage; ?></td>
							<td><?php echo $st_row['Max'] . ' ' . $uniteAffichage; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else: ?>
			<p class="no-data">Aucune statistique générable pour le moment.</p>
		<?php endif; ?>
	</section>

	<section id="mesures">
		<h2>Historique des mesures</h2>
		<div class="table-controls">
			<form method="GET" action="">
				<label for="room">Salle :</label>
				<select name="room" id="room" onchange="this.form.submit()">
					<option value="all" <?php if ($room === 'all') echo 'selected'; ?>>Toutes vos salles</option>
					<?php foreach ($liste_salles as $salle_dispo): ?>
						<option value="<?php echo $salle_dispo; ?>" <?php if ($room === $salle_dispo) echo 'selected'; ?>><?php echo $salle_dispo; ?></option>
					<?php endforeach; ?>
				</select>

				<label for="filter_type">Type :</label>
				<select name="filter_type" id="filter_type" onchange="this.form.submit()">
					<option value="all" <?php if ($filter_type === 'all') echo 'selected'; ?>>Tous les capteurs</option>
					<option value="co2" <?php if ($filter_type === 'co2') echo 'selected'; ?>>CO2</option>
					<option value="temperature" <?php if ($filter_type === 'temperature') echo 'selected'; ?>>Température</option>
					<option value="humidite" <?php if ($filter_type === 'humidite') echo 'selected'; ?>>Humidité</option>
					<option value="pressure" <?php if ($filter_type === 'pressure') echo 'selected'; ?>>Pression</option>
					<option value="tvoc" <?php if ($filter_type === 'tvoc') echo 'selected'; ?>>TVOC</option>
				</select>

				<label for="sort_by">Trier par :</label>
				<select name="sort_by" id="sort_by" onchange="this.form.submit()">
					<option value="date" <?php if ($sort_by === 'date') echo 'selected'; ?>>Date / Horaire</option>
					<option value="valeur" <?php if ($sort_by === 'valeur') echo 'selected'; ?>>Valeur</option>
				</select>

				<select name="order" id="order" onchange="this.form.submit()">
					<option value="DESC" <?php if ($order === 'DESC') echo 'selected'; ?>>Décroissant (Max)</option>
					<option value="ASC" <?php if ($order === 'ASC') echo 'selected'; ?>>Croissant (Min)</option>
				</select>
			</form>
		</div>

	<?php if (!empty($rows)): ?>
		<table id="tab_mess">
			<thead>
				<tr>
					<th>Salle</th>
					<th>Type</th>
					<th>Date</th>
					<th>Horaire</th>
					<th>Valeur</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($rows as $row): 
					$typeAffichage = isset($traductionTypes[$row['type']]) ? $traductionTypes[$row['type']] : $row['type'];
					$uniteAffichage = isset($traductionUnites[$row['unite']]) ? $traductionUnites[$row['unite']] : $row['unite'];
				?>
					<tr>
						<td><?php echo $row['Salle']; ?></td>
						<td><?php echo $typeAffichage; ?></td>
						<td><?php echo $row['date']; ?></td>
						<td><?php echo $row['horaire']; ?></td>
						<td><?php echo $row['valeur'] . ' ' . $uniteAffichage; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else: ?>
		<p class='no-data'>Aucune mesure trouvée pour les critères sélectionnés.</p>
	<?php endif; ?>
	</section>

</main>

<?php
require "includes/footer.php"; 
?>