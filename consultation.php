<?php
$page_title = "Gestion"; 
require "includes/header.php"; 
?>


<?php
require "./scripts/login.php";

$room        = isset($_GET['room'])        ? addslashes($_GET['room'])        : 'E105';
$filter_type = isset($_GET['filter_type']) ? addslashes($_GET['filter_type']) : 'all';
$sort_by     = isset($_GET['sort_by'])     ? addslashes($_GET['sort_by'])     : 'date';
$order       = isset($_GET['order'])       ? addslashes($_GET['order'])       : 'DESC';

if ($order !== 'ASC' && $order !== 'DESC') {
	$order = 'DESC';
}

$where_clause = "WHERE Capteur.nom_salle = \"$room\"";
if ($filter_type !== 'all' && $filter_type !== '') {
	$where_clause .= " AND Capteur.type = \"$filter_type\"";
}

if ($sort_by === 'valeur') {
	$order_clause = "ORDER BY Mesure.valeur $order";
} else {
	$order_clause = "ORDER BY Mesure.date $order, Mesure.horaire $order";
}

$result = query_from_bd("
	SELECT Capteur.type, Mesure.date, Mesure.horaire, Mesure.valeur, Capteur.unite
	FROM Mesure 
	JOIN Capteur ON Mesure.nom_capteur = Capteur.nom 
	$where_clause
	$order_clause
	LIMIT 10;
");

$rows = $result->fetch_all(MYSQLI_ASSOC);
?>

	<div class="table-controls">
		<form method="GET" action="">
			<input type="hidden" name="room" value="<?php echo $room; ?>">

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

<?php
if (!empty($rows)) {
	echo '<table id="tab_mess">';
	echo "<caption>Mesures " . $room . "</caption>";

	echo '<thead>';
	echo '<tr>';
	foreach (array_keys($rows[0]) as $columnName) {
		if ($columnName !== "unite") {
			echo '<th>' . ucfirst($columnName) . '</th>';
		}
	}
	echo '</tr>';
	echo '</thead>';

	echo '<tbody>';
	$traductionTypes = ['tvoc' => 'Taux de COV', 'pressure' => 'Pression', 'co2' => 'CO2', 'temperature' => 'Température', 'humidite' => 'Humidité'];
	$traductionUnites = ['pourcentage' => '%', 'celcius' => '°C', 'ppm' => 'ppm', 'ppb' => 'ppb', 'hPa' => 'hPa'];
	
	foreach ($rows as $row) {
		$typeAffichage = isset($traductionTypes[$row['type']]) ? $traductionTypes[$row['type']] : $row['type'];
		$uniteAffichage = isset($traductionUnites[$row['unite']]) ? $traductionUnites[$row['unite']] : $row['unite'];

		echo '<tr>';
		echo '<td>' . $typeAffichage . '</td>';
		echo '<td>' . $row['date'] . '</td>';
		echo '<td>' . $row['horaire'] . '</td>';
		echo '<td>' . $row['valeur'] . ' ' . $uniteAffichage . '</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
} else {
	echo "<p class='no-data'>Aucune mesure trouvée pour la salle " . $room . ".</p>";
}
?>

<?php 
require "includes/footer.php"; 
?>