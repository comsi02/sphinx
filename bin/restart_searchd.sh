#!/bin/sh
echo "#---------------------------- searching ------------------------------#\n"
~comsi02/work/sphinx/bin/searchd --config ~comsi02/work/sphinx/etc/sphinx-min.conf --stop
sleep 1
~comsi02/work/sphinx/bin/searchd --config ~comsi02/work/sphinx/etc/sphinx-min.conf
echo "#---------------------------- searching ------------------------------#\n"
