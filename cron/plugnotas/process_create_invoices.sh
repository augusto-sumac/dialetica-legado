#!/usr/bin/env bash

# Generic call a php file with the same name

# Static Vars
dir=`dirname "$0"`
dir=${dir%/}
job=`basename "$0"`
job=${job%.sh}
day=$(date +'%Y-%m-%d')
hour=$(date +'%Y-%m-%d %H:%M:%S')
log_dir=`realpath "$dir"`
log_dir=`dirname "$log_dir"`
log_dir=`dirname "$log_dir"`
log_dir="${log_dir}/storage/logs/cron/${job}"
log_file="${log_dir}/${day}.log"
php_bin=/usr/local/bin/php

if [[ -f "/opt/cpanel/ea-php74/root/usr/bin/php" ]]
then
    php_bin=/opt/cpanel/ea-php74/root/usr/bin/php
fi

if [[ -f "/usr/bin/php" ]]
then
    php_bin=/usr/bin/php
fi

# Sleep prevent same time both runs
t=$((1 + $RANDOM % 5))
sleep ${t}

# get current script pid
echo "${dir}.*${job}.php"
pid=$(ps aux | grep -i "${dir}.*${job}.php" | grep -v grep | grep -v jailshell)

mkdir -p ${log_dir}

touch ${log_file}

echo "${hour} - Try to start" >> "${log_file}"

if [ -z "$pid" ]
then
    $php_bin "${dir}/${job}.php" >> "${log_file}"
else
    echo "${hour} - Already is running $pid - BASHPID${BASHPID}" >> "${log_file}"
fi