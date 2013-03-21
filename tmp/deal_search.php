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

        $attr = array('host','post','index','sortby','sortexpr','groupby','groupsort','distinct','limit','offset');
        $mode = array(
                        'any'       => SPH_MATCH_ANY,
                        'boolean'   => SPH_MATCH_BOOLEAN,
                        'extended'  => SPH_MATCH_EXTENDED,
                        'extended2' => SPH_MATCH_EXTENDED2,
                        'phrase'    => SPH_MATCH_PHRASE
                     );
        $rank = array(
                        'bm25'      => SPH_RANK_BM25,
                        'none'      => SPH_RANK_NONE,
                        'wordcount' => SPH_RANK_WORDCOUNT,
                        'fieldmask' => SPH_RANK_FIELDMASK,
                        'sph04'     => SPH_RANK_SPH04
                     );
        
        foreach ($attr as $a) {
            if (isset($args[$a])) $this->$a = $args[$a];
        }

        foreach ($mode as $key => $val) {
            if ($args[$key] == true) $this->mode = $val;
        }

        foreach ($rank as $key => $val) {
            if ($args[$key] == true) $this->ranker = $val;
        }

        print_r($args['filter']); 
        


        return true;
    }
}

$args = array();
$args['host']  = '127.0.0.1';
$args['port']  = '9312';
$args['index'] = 'idx_deal_AV';
$args['any']   = true;
$args['bm25']  = true;
$args['filter'] = array();
array_push($args['filter'],array('catetory_srl' => 1000));
array_push($args['filter'],array('main_deal_srl' => 123456));

$sphinx = new Sphinx ();

print "==================== RESULT ============================\n";

print_r($sphinx->search($args));
#print_r($sphinx);
#print_r($args);

