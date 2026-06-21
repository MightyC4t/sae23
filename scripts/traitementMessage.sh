#!/bin/bash

SCRIPT_DIR=$(dirname "$(realpath "$0")")

cd "$SCRIPT_DIR" || exit 1

# Check arguments and display usage if needed
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

# Load database credentials
source ./config.sh
export MYSQL_PWD="$MYSQL_PASSWORD"

topic="sensors/AM107/by-room/$salle/data"

echo -e "\033[3mEn attente de la réception d'un message...\033[0m"

# Fetch 1 MQTT message
message="$(mosquitto_sub -h mqtt.iut-blagnac.fr -u student -P student -p 8883 -t $topic -W 3 -C 1 -v)"

echo "Message reçu !"

# Parse JSON payload
valeur=$(echo $message | cut -d " " -f 2- | jq ".[0]")

# Get room sensors from DB
requete_capteurs=$(/opt/lampp/bin/mysql -u "$MYSQL_USER" "$MYSQL_DB" -e "
    SELECT nom_capteur, Capteur.type
    FROM Capteur 
    JOIN Salle ON Capteur.nom_salle = Salle.nom_salle 
    WHERE Salle.nom_salle = '$salle'" --batch)

while IFS= read -r ligne; do
    capteurs+=("$ligne")
done < <(echo "$requete_capteurs" | tail -n +2)

# Save to DB or print values
if [ "$mode_publish" = true ]; then
    for capteur in "${capteurs[@]}"; do
        id=$(echo "$capteur" | cut -f 1)
        type=$(echo "$capteur" | cut -f 2)

        donnee=$(echo $valeur | jq ".$type")

        # Insert measurement into DB
        /opt/lampp/bin/mysql -u "$MYSQL_USER" "$MYSQL_DB" -e "
        INSERT INTO Mesure (id_mes, date, horaire, valeur, nom_capteur) VALUES 
        (NULL, CURDATE(), CURTIME(), $donnee, \"$id\")" 2>>error.log

        if [ $? -ne 0 ]; then
            echo "MySQL error occurred at $(date)" >> error.log
        fi
    done
else
    echo "$salle : $temperature $humidite $co2 $tvoc $pression"
fi