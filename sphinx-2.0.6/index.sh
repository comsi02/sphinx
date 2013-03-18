#!/bin/sh
echo "#---------------------------- indexing ------------------------------#\n"
./bin/indexer --config ./etc/sphinx-min.conf --print-queries deal
echo "#---------------------------- indexing ------------------------------#\n"
