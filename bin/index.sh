#!/bin/sh
echo "#---------------------------- indexing ------------------------------#\n"
~comsi02/work/sphinx/bin/indexer --config ~comsi02/work/sphinx/etc/sphinx-min.conf --all --rotate
echo "#---------------------------- indexing ------------------------------#\n"
