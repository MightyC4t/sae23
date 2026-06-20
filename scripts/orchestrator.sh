SCRIPT_DIR=$(dirname "$(realpath "$0")")

cd "$SCRIPT_DIR" || exit 1

RESET_COLOR="\033[0m"

salles=("E106" "E208" "B103" "B113")

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