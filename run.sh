#!/bin/bash

echo "[RUN SCRIPT] Setting up trap ..."

prep_term() {
    unset term_child_pid
    unset term_kill_needed
    trap 'handle_term' TERM INT
}

handle_term() {
    if [ "${term_child_pid}" ]; then
        kill -TERM "${term_child_pid}" 2>/dev/null
    else
        term_kill_needed="yes"
    fi
}

wait_term() {
    term_child_pid=$!
    if [ "${term_kill_needed}" ]; then
      kill -TERM "${term_child_pid}" 2>/dev/null
    fi
    wait ${term_child_pid} 2>/dev/null
    trap - TERM INT
    wait ${term_child_pid} 2>/dev/null
}

# Prepare termination signal
prep_term

echo "[RUN SCRIPT] Trap setup completed ..."

# Include all your commands to this block
###############################################################
echo "[RUN SCRIPT] Starting app ...";

echo "[RUN SCRIPT] APPLICATION is $APPLICATION"

# echo "[RUN SCRIPT] Start - Printing env variables file"
# ls -laXh /var/www/autopay
# cat /var/www/autopay/*
# echo "[RUN SCRIPT] End - Printing env variables file"



# Select application to run
case $APPLICATION in
   core_worker)
        echo "Running worker"
        # Configure bash to exit when some command fails
        set -e

        echo "[RUN SCRIPT] Configuring caching ..."
        ./artisan config:cache
        ./artisan route:cache
        ./artisan view:cache

         echo "[RUN SCRIPT] Running migrations ..."
        ./artisan migrate --database=pgsql_direct --verbose --force

        echo "[RUN SCRIPT] Running setup ..."
        ./artisan setup || { echo "Setup command failed. Please check logs." && exit 1; }

        echo "[RUN SCRIPT] Pause for 60 seconds (to make sure previous worker is stopped) ..."
        sleep 60

        echo "[RUN SCRIPT] Starting SupervisorD ..."
        ./artisan horizon &
        wait_term
        echo "[RUN SCRIPT] Received stop signal, stopping app ..."
        ;;

    core_producer)
        echo "Running octane"
        # Configure bash to exit when some command fails
        set -e

        echo "[RUN SCRIPT] Configuring caching ..."
        ./artisan config:cache
        ./artisan route:cache
        ./artisan view:cache

        ./artisan octane:start \
            --server=swoole \
            --host=0.0.0.0 \
            --port=8000 \
            --workers=15 \
            --task-workers=5 \
            --max-requests=500 \
            --no-interaction &
        wait_term
        echo "[RUN SCRIPT] Received stop signal, stopping app ..."
        ;;

    cronjob)
        echo "Running cronjob"

        ./artisan schedule:run &

        wait_term
        echo "[RUN SCRIPT] Received stop signal, stopping app ..."
        ;;

    development)
        echo "Running development server"
        set -e

        echo "[RUN SCRIPT] Configuring caching ..."
        ./artisan config:cache
        ./artisan route:cache
        ./artisan view:cache

        echo "[RUN SCRIPT] Running migrations ..."
        ./artisan migrate --verbose --force

        echo "[RUN SCRIPT] Running setup ..."
        ./artisan setup || { echo "Setup command failed. Please check logs." && exit 1; }

        ./artisan serve \
            --host=0.0.0.0 \
            --port=8000 &

        wait_term
        ;;
    *)
        echo "[RUN SCRIPT] No application specified"
        ;;
esac
###############################################################

# Do some cleanup if needed

echo "[RUN SCRIPT] Stopped, exiting from script ...";
