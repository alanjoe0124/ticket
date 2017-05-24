#!/bin/sh

echo "dump"
mysqldump -uroot -d ticket > /tmp/ticket.sql
echo "import"
mysql -uroot -e "DROP DATABASE IF EXISTS ticket_test; CREATE DATABASE ticket_test;"
mysql -uroot ticket_test < /tmp/ticket.sql
echo "done"

rm /tmp/ticket.sql
