<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8" />
	<title><?php echo isset($page_title) ? $page_title : 'SAE23'; ?></title>
	<link rel="shortcut icon" href="favicon.png" type="image/png">

	<script>
		const savedTheme = localStorage.getItem('theme-preference');
		const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
		const shouldBeDark = savedTheme === 'dark' || (!savedTheme && prefersDark);

		if (shouldBeDark) {
			document.documentElement.classList.add('dark');
		}
		window.__themeInitialState = shouldBeDark;
	</script>

	<link rel="stylesheet" href="styles/style.css">
	<link rel="stylesheet" href="styles/dark-mode.css">
	<?php
	if (isset($styles)) {
		foreach ($styles as $style) {
			echo '<link rel="stylesheet" href="'. $style .'">';
		}
	}
	?>
</head>

<body>
	<header>
		<div class="theme-switch">
			<label for="theme">Mode sombre</label>
			<input type="checkbox" name="theme" id="theme">

			<script>
				const themeCheckbox = document.getElementById('theme');
				themeCheckbox.checked = window.__themeInitialState;

				themeCheckbox.addEventListener('change', () => {
					if (themeCheckbox.checked) {
						localStorage.setItem('theme-preference', 'dark');
						document.documentElement.classList.add('dark');
					} else {
						localStorage.setItem('theme-preference', 'light');
						document.documentElement.classList.remove('dark');
					}
				});
			</script>
		</div>
		<nav>	
			<?php
			if (isset($page_title) && $page_title != 'Consultation') {
				echo "
				<ul>
					<li><a href=\"index.php\">Accueil</a></li>
					<li><a href=\"administration.php\">Administration</a></li>
					<li><a href=\"gestion.php\">Gestion</a></li>
					<li><a href=\"consultation.php\">Consultation</a></li>
					<li><a href=\"gestion_de_projet.php\">Gestion de projet</a></li>
					<li><a href=\"mentions_legales.php\">Mentions légales</a></li>
				</ul>
				";
			} else {
				echo "<ul>
						<li><a href=\"index.php\">Accueil</a></li>";
				foreach (["E106", "E208", "B103", "B113"] as $roomOption) {
					echo "<li><a href=\"?room=$roomOption\">$roomOption</a></li>";
				}
				echo "<li><a href=\"mentions_legales.php\">Mentions légales</a></li>
					</ul>";

			}
			?>
		</nav>
	</header>