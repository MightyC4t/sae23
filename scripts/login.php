<?php
    echo "HEHE";

    $id_bd = mysqli_connect("127.0.0.1", "mmoutonnet","dbpassword", "sae23") 
        or die("Connexion au serveur et/ou à la base de données impossible !");

    mysqli_query($id_bd,"SET NAMES 'utf8'");

    // $resultat = mysqli_query($id_bd,"SELECT * from Batiment")
    //     or die("Exécution de la requête impossible");

    // mysqli_close($id_bd);

    // while ($ligne = mysqli_fetch_assoc($resultat))
    //     foreach ($ligne as $cle => $valeur)
    //         echo "$cle :: $valeur <br>";
?>