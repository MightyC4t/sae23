#!/bin/bash

SCRIPT_DIR=$(dirname "$(realpath "$0")")

cd "$SCRIPT_DIR" || exit 1

# If we pass nothing or "help" then print correct usage
if [ -z "$1" ] ||  [ $1 == "help" ]; then
        echo "usage: $0 [help] | <salle>"
        echo "Exemple : $0 E105"
        exit 0
else
    salle=$1
fi

# Subscription part

# We collect the data from the 2nd floor
topic="sensors/AM107/by-room/$salle/data"

echo -e "\033[3mEn attente de la réception d'un message...\033[0m"

# 3 rooms per floor
message="$(mosquitto_sub -h mqtt.iut-blagnac.fr -u student -P student -p 8883 -t $topic -W 3 -C 1 -v)"

while read -r ligne_du_message; do
    valeur=$(echo $ligne_du_message | cut -d " " -f 2- | jq ".[0]")

    # echo "On a reçu ==> $capteur $valeur"

    # Extracting data

    temperature=$(echo $valeur | jq ".temperature")
    humidite=$(echo $valeur | jq ".humidity")
    co2=$(echo $valeur | jq ".co2")
    tvoc=$(echo $valeur | jq ".tvoc")
    pression=$(echo $valeur | jq ".pressure")

    # Saving data 

    echo "$temperature $humidite $co2 $tvoc $pression"


    # Supprimer ce commentaire à posteriori
    # Créer une boucle qui itère parmis les données (la variable $data)
    /opt/lampp/bin/mysql -u mmoutonnet -p'dbpassword' sae23 -e "
                                INSERT INTO Mesure (id_mes, date, horaire, valeur, nom_capteur) VALUES 
                                (NULL, CURDATE(), CURTIME(), $data, 'Capteur_temp_$salle');"

done <<< $message