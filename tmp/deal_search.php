<?php

require_once( "sphinxapi.php" );

class Sphinx {

    /**
    * Sphinx Search Class Construct by comsi02
    *
    * @access   public
    * @return   void
    * @param    null
    */
    function __construct () {
        $this->ci         = new SphinxClient ();
        $this->q          = "";
        $this->sql        = "";
        $this->mode       = SPH_MATCH_ALL;
        $this->host       = "127.0.0.1";
        $this->port       = 9312;
        $this->index      = "*";
        $this->groupby    = "";
        $this->groupsort  = "@group desc";
        $this->filter     = "group_id";
        $this->filtervals = array();
        $this->distinct   = "";
        $this->sortby     = "";
        $this->sortexpr   = "";
        $this->limit      = 20;
        $this->offset     = 0;
        $this->ranker     = SPH_RANK_PROXIMITY_BM25;
        $this->select     = "";
    }
    
    function search ($args) {

        if (isset($args['host'])) $this->host = $args['host'];

        return true;
    }
}

$args = array();
$args['host'] = 'tmon.co.kr';

$sphinx = new Sphinx ();

print "==================== RESULT ============================\n";
#print_r($sphinx);

print_r($sphinx->search($args));
print "\n";
print_r($sphinx->host);
print "\n";

?>
