<?php
$page_title = "Consultation"; 
require "includes/header.php"; 
require "./scripts/db_query.php";

$result = query_from_db("
	SELECT Capteur.nom_salle AS salle, Capteur.type, Mesure.date, Mesure.horaire, Mesure.valeur, Capteur.unite
	FROM Mesure 
	JOIN Capteur ON Mesure.nom_capteur = Capteur.nom_capteur 
	JOIN (
	-- 
		SELECT nom_capteur, MAX(CONCAT(Mesure.date, ' ', Mesure.horaire)) AS max_moment
		FROM Mesure
		GROUP BY nom_capteur
	) derniere_mesure ON Mesure.nom_capteur = derniere_mesure.nom_capteur  
	AND CONCAT(Mesure.date, ' ', Mesure.horaire) = derniere_mesure.max_moment
	ORDER BY Capteur.nom_salle ASC, Capteur.type ASC;
");

$rows = $result->fetch_all(MYSQLI_ASSOC);

if (!empty($rows)) {
	echo '
	<table id="tab_mess">
		<caption>Dernières mesures de chaque bâtiment / salle</caption>
		<thead>
			<tr>
				<th>Salle</th>
				<th>Type</th>
				<th>Date</th>
				<th>Horaire</th>
				<th>Valeur</th>
			</tr>
		</thead>
		<tbody>';
	$traductionTypes = ['tvoc' => 'Taux de COV', 'pressure' => 'Pression', 'co2' => 'CO2', 'temperature' => 'Température', 'humidity' => 'Humidité'];
	$traductionUnites = ['pourcentage' => '%', 'celcius' => '°C', 'ppm' => 'ppm', 'ppb' => 'ppb', 'hPa' => 'hPa'];
	
	# $row is an assossiative array
	foreach ($rows as $row) {
		$typeAffichage = $traductionTypes[$row['type']];
		$uniteAffichage = $traductionUnites[$row['unite']];

		echo '<tr>';
		echo '<td>' . $row['salle'] . '</td>';
		echo '<td>' . $typeAffichage . '</td>';
		echo '<td>' . $row['date'] . '</td>';
		echo '<td>' . $row['horaire'] . '</td>';
		echo '<td>' . $row['valeur'] . ' ' . $uniteAffichage . '</td>';
		echo '</tr>
		';
	}
	echo '</tbody>';
	echo '</table>';
} else {
	echo "<p class='no-data'>Aucune mesure trouvée dans la base de données.</p>";
}

require "includes/footer.php"; 
?>