#!/bin/sh
echo 
echo 
echo "#---------------------------- Start Searchd ------------------------------#"
echo
~comsi02/work/sphinx/bin/searchd --config ~comsi02/work/sphinx/etc/sphinx-min.conf --status
