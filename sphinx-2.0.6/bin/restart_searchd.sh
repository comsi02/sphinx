#!/bin/sh
echo "#---------------------------- searching ------------------------------#\n"
~comsi02/work/sphinx/sphinx/bin/searchd --config ~comsi02/work/sphinx/sphinx/etc/sphinx-min.conf --stop
~comsi02/work/sphinx/sphinx/bin/searchd --config ~comsi02/work/sphinx/sphinx/etc/sphinx-min.conf
echo "#---------------------------- searching ------------------------------#\n"
