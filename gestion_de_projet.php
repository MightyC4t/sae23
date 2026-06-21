<?php
$page_title = "Gestion de projet"; 
require "includes/header.php"; 
?>
<main>
    <section>
        <h2>Introduction et Objectifs</h2>
        <p>Dans le cadre de cette SAÉ 23, il nous est demandé de rédiger, à la fin de chaque séance de TP, un compte-rendu détaillant l'ensemble des tâches accomplies. Avant la fin de la SAÉ, nous devons réaliser un bilan complet comprenant le diagramme de Gantt initial et final, ainsi qu'une présentation de l'outil collaboratif utilisé au sein du groupe (qui permet de suivre la répartition des tâches de chaque membre). De plus, chaque membre de l'équipe doit obligatoirement rédiger, avant la clôture du projet, une synthèse personnelle et précise du travail qu'il a réalisé. Enfin, nous devons expliciter les problèmes rencontrés ainsi que les solutions apportées pour y pallier, avant de conclure sur notre niveau de satisfaction vis-à-vis du cahier des charges.</p>
    </section>

    <section>
        <h2>Planification et Outils Collaboratifs</h2>
        
        <article>
            <h3>Diagramme de Gantt prévisionnel</h3>
            <p>Comme l'illustre cette capture d’écran, la planification initiale met en évidence les tâches majeures programmées lors des séances encadrées des 22 et 29 mai. Bien que la continuité de la SAÉ 23 ait été planifiée sur les créneaux de TP en autonomie, un volume important de travail a également été fourni à distance, de manière individuelle. Le lien hypertexte situé sous l'image vous permettra de télécharger le diagramme de Gantt complet afin d'en consulter les détails.</p>
          
            <p>Voici donc le diagramme de Gant réel : (IL FAUT LE CHANGER)<br>Explication de pourquoi il est comme ça</p>
        </article>

        <article>
            <h3>Utilisation de Notion et Suivi Kanban</h3>
            <p>Dans le cadre de cette SAÉ 23, notre équipe a choisi d'exploiter l'outil collaboratif Notion. Similaire à l'application Trello, cette plateforme permet de centraliser en un point unique des notes, de la documentation, des bases de données ainsi que des fonctionnalités de gestion de projet. Pour ce travail, nous avons particulièrement sollicité le tableau Kanban, un outil qui s'est avéré précieux pour suivre l'avancement des livrables et respecter les différentes échéances, comme le montre l'illustration ci-dessous :</p>
            <p>Sur cette capture d’écran, on remarque les tâches qui n’ont pas encore commencé, comme la « Gestion de Projet ». Néanmoins, nous pouvons situer clairement les projets en cours ainsi que ceux qui sont terminés. C’est le cas par exemple pour « Grafana » et « Node-RED », dont le réalisateur est Adam, ce qui est facilement remarquable grâce au nom inscrit en dessous de chaque tâche.</p>
        </article>
    </section>

    <section>
        <h2>Synthèse des Tâches par Membre</h2>
        <p>En ce qui concerne la synthèse des tâches que chaque membre du groupe doit réaliser, les voici :</p>
        
        <article>
            <h3>Adam</h3>
            <p>Adam a déployé et sécurisé l'intégralité de la chaîne IoT en configurant quatre conteneurs Docker (mosquittoRT, influxdbRT, noderedRT, grafanaRT) configurés en redémarrage automatique. Il a interconnecté ces services en établissant une liaison MQTT sécurisée (port 8883) pour collecter le flux de l'IUT via le topic iut/#, avant de concevoir un flux Node-RED pour filtrer, convertir en JavaScript et stocker dans InfluxDB les données de quatre salles cibles (E208, E105, B112, B113). Pour valider le bon fonctionnement de cette infrastructure de l'envoi jusqu'au stockage, il a simulé avec succès l'injection de données locales via des commandes mosquitto_pub, puis a finalisé un tableau de bord Grafana organisé en quadrants par mesure (températures, CO2, luminosité). Enfin, il a consigné son travail dans un document de secours contenant des captures d'écran et un lien de partage actif, tout en prévoyant d'ajuster ultérieurement le choix de certaines salles selon les directives reçues.</p>
        </article>

        <article>
            <h3>Mathys</h3>
            <p>J'ai réalisé la partie du site web en lien avec la base de données. Cela regroupe la partie Administration et Gestion, qui sont profondément liées dans la façon dont on se connecte. J'ai également réalisé la partie Consultation, qui m'a facilité le travail de création de la partie "Consultation" des gestionnaires. Pour une simplicité et une uniformité de code, j'ai également créé la partie "includes" qui crée dynamiquement la partie statique des pages que sont l'header et le footer des pages, bien qu'à un niveau écoconception il aurait été préférable de générer les pages, puis de les faire récupérer par le client (simplicité de code mais performances amoindries).</p>
        </article>

        <article>
            <h3>Miguel</h3>
            <p>Dans le cadre de cette SAÉ 23, Miguel a atteint les objectifs fixés en finalisant la base de données relationnelle du site web via phpMyAdmin, une tâche exigeante qui a nécessité une réinstallation complète du serveur local XAMPP pour recréer l'environnement de travail suite à une absence, avant de partager l'avancement sur GitHub. En parallèle de ce travail technique, il s'est pleinement chargé du pilotage et de la structure organisationnelle du projet en réalisant de manière autonome le compte-rendu de TP ainsi que le diagramme de Gantt. Enfin, il a assuré la centralisation et la visibilité de ces éléments de gestion de projet en s'occupant personnellement de leur intégration et de leur mise en ligne directement sur le site web de l'équipe.</p>
        </article>

        <article>
            <h3>Ludovic</h3>
            <p>S'est chargé du déploiement initial et de la prise en main de la base de données InfluxDB. Bien qu'il ait rencontré quelques difficultés lors de la phase de démarrage notamment concernant la compréhension du modèle de données de type séries temporelles et le fonctionnement des <em>measurements</em>.</p>
        </article>
    </section>

    <section>
        <h2>Difficultés Rencontrées et Analyse</h2>
        <p>Dans le cadre de cette SAÉ 23, notre équipe a été confrontée à plusieurs difficultés, qu'elles soient d'ordre organisationnel ou technique. Il apparaît essentiel d'analyser ces incidents et d'en comprendre l'origine afin d'en tirer un retour d'expérience constructif.</p>
        <p>Le premier obstacle a résidé dans un manque initial de coordination pour la répartition des tâches, ce qui a engendré des retards sur notre planning global. Ce problème s'explique par différents facteurs. D'une part, l'un des membres, Miguel Nascimento Silva, envisageant une réorientation hors du BUT R&amp;T et rencontrant de grandes difficultés avec les langages de programmation, n'a pu prendre en charge que une petite partie de la création des pages HTML et PHP. Cela a contraint les autres membres de l'équipe à développer un volume de pages plus important que prévu pour compenser, ce qui a ralenti le rythme de progression global et surchargé le reste du groupe.</p>
        <p>D'autre part, durant les premiers jours, Ludovic Henin — initialement chargé de la base de données InfluxDB — a dû s'absenter ponctuellement pour combler un retard accumulé sur la SAÉ 21. Ces absences répétées ont créé un déséquilibre dans la charge de travail collective, limitant sa contribution finale à la seule gestion d'InfluxDB.</p>
        <p>En conséquence, la répartition des tâches s'est avérée très asymétrique. Pour tenter de pallier ce manque d'avancement, Mathys Moutonnet Olivera a pris l'initiative de prendre en charge une part importante du développement du site web, pensant ainsi accélérer le projet, ce qui a accentué le déséquilibre au sein du groupe.</p>
        <p>Enfin, sur le plan technique, l'équipe a été bloquée dès le premier TP en autonomie par un incident réseau sur Docker. Ce dysfonctionnement réseau a causé un retard non négligeable pour Mathys et Adam.</p>
    </section>

    <section>
        <h2>Solutions Apportées et Perspectives d'Amélioration</h2>
        <p>Afin de pallier ces différents problèmes et d'éviter qu'ils ne se reproduisent, le groupe a mis en place les solutions suivantes :</p>
        <ul>
            <li>
                <strong>Résolution de l'incident Docker :</strong> Après une phase de débogage, le problème réseau a été résolu en relançant proprement le service Docker, ce qui a permis de recréer automatiquement les routes indispensables au bon fonctionnement de la chaîne IoT.
            </li>
            <li>
                <strong>Gestion des absences :</strong> Bien que ce problème de gestion du temps n'ait pas pu être pleinement résolu durant les séances officielles de la SAÉ 23, l'équipe aurait dû convenir d'une règle stricte : en cas d'absence ou de travail sur un autre projet pendant un créneau dédié, le membre concerné doit obligatoirement rattraper ses heures sur son temps libre afin de ne pas bloquer l'avancement collectif.
            </li>
            <li>
                <strong>Rééquilibrage du code :</strong> Pour corriger la centralisation excessive du développement du site web par Mathys, nous avons décidé que chaque membre du groupe devait analyser et étudier le code produit. Cette démarche vise à ce que chacun comprenne l'architecture de l'application et puisse monter en compétences.
            </li>
        </ul>
    </section>

    <footer>
        <h2>Conclusion</h2>
        <p>En conclusion, malgré les défis organisationnels et techniques rencontrés au cours du projet, l'équipe a su réagir avec efficacité pour remplir l'intégralité du cahier des charges. Toutes les étapes clés ont été franchies avec succès : de la mise en œuvre de la chaîne de traitement conteneurisée au déploiement des bases de données InfluxDB and MySQL, jusqu'à la restitution des données environnementales via le dashboard Grafana et le site web dynamique. Les contraintes de droits d'accès pour les administrateurs et les gestionnaires ont été scrupuleusement respectées, et l'environnement technique sous Lubuntu est entièrement fonctionnel. Ce projet valide ainsi la conformité de notre livrable face aux exigences initiales et témoigne de notre capacité à mener à bien un projet d'infrastructure et de développement web de bout en bout.</p>
    </footer>
</main>
<?php 
require "includes/footer.php"; 
?>
