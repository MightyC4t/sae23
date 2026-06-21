#!/bin/bash

SCRIPT_DIR=$(dirname "$(realpath "$0")")

cd "$SCRIPT_DIR" || exit 1

RESET_COLOR="\033[0m"

#!/bin/bash

source ./config.sh
export MYSQL_PWD="$MYSQL_PASSWORD"

requete=$(/opt/lampp/bin/mysql -u "$MYSQL_USER" "$MYSQL_DB" -e "SELECT nom_salle FROM Salle" --batch)

# Process the request line by line and skipping the row's name
# 'IFS=' and 'read -r' ensure lines are read
# 'tail -n +2' removes the first line ("nom_salle")
while IFS= read -r ligne; do
    salles+=("$ligne")
done < <(echo "$requete" | tail -n +2)
# By here we have all rooms at our disposal

while true; do
    for salle in "${salles[@]}"; do
        echo -e "\033[3;32mOn analyse la salle$RESET_COLOR $salle"
        ./traitementMessage.sh "$salle" publish &> /dev/null
        sleep 3
    done

    date "+%d-%m-%Y %H:%M:%S" > last_run.txt

    for i in {600..0}; do
        echo -ne "Temps restant : $i \033[0K\r"
        sleep 1
    done
    echo -e "\n\033[0;32mC'est reparti ! $RESET_COLOR"
done