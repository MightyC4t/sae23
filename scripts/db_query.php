<?php
    function query_from_db($query)
    {
        $id_bd = mysqli_connect("127.0.0.1", "mmoutonnet","dbpassword", "sae23") 
            or die("Connexion au serveur et/ou à la base de données impossible !");

        mysqli_query($id_bd,"SET NAMES 'utf8'");

        $resultat = mysqli_query($id_bd, $query)
            or die("Exécution de la requête impossible <br>
            La requête <pre>$query</pre>");

        mysqli_close($id_bd);

        return $resultat;
    }
?>