#!/bin/sh
echo "#---------------------------- indexing ------------------------------#\n"
~comsi02/work/sphinx/sphinx/bin/indexer --config ~comsi02/work/sphinx/sphinx/etc/sphinx-min.conf --print-queries --all
echo "#---------------------------- indexing ------------------------------#\n"
