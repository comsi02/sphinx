#!/bin/sh
echo "#---------------------------- indexing ------------------------------#\n"
./indexer --config ./sphinx.conf --print-queries --all --rotate --dump-rows index_debug.log
echo "#---------------------------- indexing ------------------------------#\n"
