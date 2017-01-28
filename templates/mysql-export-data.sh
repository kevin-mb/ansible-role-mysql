#!/bin/bash

USER={{ mysql_root_username }}
PASS={{ mysql_root_password }}
HOST=localhost

MYSQL=$(mysql -N -u${USER} -p${PASS} -h${HOST} <<< "SHOW DATABASES" | grep -v mysql | grep -v information_schema | grep -v performance_schema | tr "\n" " ")

mysqldump -v -u${USER} -p${PASS} -h${HOST} --routines --triggers --databases  --skip-lock-tables ${MYSQL} > /data/www/dev-data.sql
