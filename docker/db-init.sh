#!/bin/bash
# Import the WordPress database with --force to skip system table errors
echo "Starting database import..."
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" "${MYSQL_DATABASE}" --force < /var/dump.sql
echo "Database import finished."
