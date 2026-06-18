<?php
$page_title = "Accueil SAE23"; 
require "includes/header.php"; 
?>

<main class="home-container">
	<h1>Bienvenue sur le portail de suivi de la SAE 23</h1>
	<p>Ce site permet de superviser l'état des capteurs environnementaux au sein des différents bâtiments de l'IUT.</p>
	
	<section class="quick-links">
		<h2>Raccourcis de navigation</h2>
		<p>Utilisez le menu ci-dessus ou cliquez sur l'un des accès directs pour démarrer :</p>
		<ul>
			<li>Consulter les dernières mesures en temps réel : <a href="consultation.php">Accéder aux mesures</a></li>
			<li>Gérer le parc de capteurs et les salles : <a href="gestion.php">Espace Gestion</a></li>
		</ul>
	</section>
</main>

<?php 
require "includes/footer.php"; 
?>