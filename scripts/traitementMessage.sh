#!/bin/bash

SCRIPT_DIR=$(dirname "$(realpath "$0")")

cd "$SCRIPT_DIR" || exit 1

# If we pass nothing or "help" then print correct usage
if [ -z "$1" ] ||  [ $1 == "help" ]; then
echo "usage: $0 [help] | <salle> [publish]"
    echo "Exemples :"
    echo "  $0 E106          (Exécute le script normalement pour la salle E106)"
    echo "  $0 E106 publish  (Exécute et publie les résultats)"
    exit 0
else
    salle=$1
fi

mode_publish=false

if [ ! -z "$2" ]; then
    if [ "$2" == "publish" ]; then
        mode_publish=true
    else
        echo "Erreur : Argument '$2' inconnu."
        echo "Exécutez '$0 help' pour voir une façon correcte d'exécuter la commande."
    fi
fi

# Subscription part

# We collect the data from the 2nd floor
topic="sensors/AM107/by-room/$salle/data"

echo -e "\033[3mEn attente de la réception d'un message...\033[0m"

# 3 rooms per floor
message="$(mosquitto_sub -h mqtt.iut-blagnac.fr -u student -P student -p 8883 -t $topic -W 3 -C 1 -v)"

valeur=$(echo $message | cut -d " " -f 2- | jq ".[0]")

# Extracting data

temperature=$(echo $valeur | jq ".temperature")
humidite=$(echo $valeur | jq ".humidity")
co2=$(echo $valeur | jq ".co2")
tvoc=$(echo $valeur | jq ".tvoc")
pression=$(echo $valeur | jq ".pressure")

# Saving data 


if [ "$mode_publish" = true ]; then
    /opt/lampp/bin/mysql -u "mmoutonnet" -p"dbpassword" "sae23" -e "
    INSERT INTO Mesure (id_mes, date, horaire, valeur, nom_capteur) VALUES  
    (NULL, CURDATE(), CURTIME(), $temperature, 'Temp_${salle}'),
    (NULL, CURDATE(), CURTIME(), $humidite, 'Hum_${salle}'),
    (NULL, CURDATE(), CURTIME(), $co2, 'CO2_${salle}'),
    (NULL, CURDATE(), CURTIME(), $tvoc, 'TVOC_${salle}'),
    (NULL, CURDATE(), CURTIME(), $pression, 'Press_${salle}');
    " 
else
    echo "$salle : $temperature $humidite $co2 $tvoc $pression"
fi
