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
    function __construct() {
        $this->cl         = new SphinxClient ();
        $this->query      = "";
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
        $this->timeout    = 1;
    }
    
    function search($args) {

        $attr = array('host','post','index','sortby','sortexpr','groupby','groupsort','distinct','limit','offset','timeout','query');
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
        
        /* attr  setting */
        foreach ($attr as $a) {
            if (isset($args[$a])) {
                if ($a == 'sortby')   { $this->sortexpr = ""; }
                if ($a == 'sortexpr') { $this->sortby = ""; }
                $this->$a = $args[$a];
            }
        }

        /* mode setting */
        foreach ($mode as $key => $val) {
            if ($args[$key] == true) $this->mode = $val;
        }

        /* ranker setting */
        foreach ($rank as $key => $val) {
            if ($args[$key] == true) $this->ranker = $val;
        }

        /* Sphinx connection and etc setting */
        $this->cl->SetServer($this->host, $this->port);
        $this->cl->SetConnectTimeout($this->timeout);
        $this->cl->SetArrayResult(true);
        $this->cl->SetWeights(array(100,1));
        $this->cl->SetMatchMode($this->mode);
        $this->cl->SetMatchMode(0);
        $this->cl->SetRankingMode ( $this->ranker );

        /* Sphinx filter setting */
        foreach($args['filter'] as $filter) {
            foreach($filter as $key => $values) {
                $this->cl->SetFilter($key, $values);
            }
        }

        /* Sphinx filter range setting */
        foreach($args['filter_range'] as $filter_range) {
            foreach($filter_range as $key => $values) {
                print "$key -> $values[0] -> $values[1]\n";
                $this->cl->SetFilterRange($key, $values[0], $values[1]);
            }
        }

        /* Sphinx etc setting */
        if ($this->groupby)  $this->cl->SetGroupBy       ($args->groupby, SPH_GROUPBY_ATTR, $args->groupsort);
        if ($this->sortby)   $this->cl->SetSortMode      (SPH_SORT_EXTENDED, $this->sortby);
        if ($this->sortexpr) $this->cl->SetSortMode      (SPH_SORT_EXPR, $this->sortexpr);
        if ($this->distinct) $this->cl->SetGroupDistinct ($this->distinct);
        if ($this->select)   $this->cl->SetSelect        ($this->select);
        if ($this->limit)    $this->cl->SetLimits        ($this->offset * $this->limit, $this->limit, ($this->limit>1000) ? $this->limit : 1000);

        /* Sphinx search by query */
        $res = $this->cl->Query ( $this->query, $this->index );

        /* Sphinx result create */
        $return_val = array();

        if ($res == false) {
            $return_val = array( 
                            'result'      => false,
                            'total'       => 0,
                            'total_found' => 0,
                            'time'        => 0,
                            'words'       => array(),
                            'ids'         => array(),
                            'errmsg'      => $this->cl->_error,
                            'resconn'     => $this->cl->_connerror,
                           );
        } else {

            $ids = array();

            foreach ($res['matches'] as $docinfo) {
                array_push($ids,$docinfo['id']);
            }

            $return_val = array( 
                            'result'      => true,
                            'total'       => $res['total'],
                            'total_found' => $res['total_found'],
                            'time'        => $res['time'],
                            'words'       => $res['words'],
                            'ids'         => $ids,
                           );
        }

        return $return_val;
    }
}


print "==================== Sphinx Prams Setting ============================\n";

$args = array();
#$args['host'] = 'comsi02.tmonc.net';
$args['index'] = 'idx_deal_WT';
$args['offset'] = 1;
$args['limit'] = 20;
$args['filter'] = array();
$args['filter_range'] = array();
$args['query'] = "점";
#array_push($args['filter'],array('main_category_srl' => array(1000)));
#array_push($args['filter'],array('category_srl' => array(1120)));
#array_push($args['filter_range'],array('start_date' => array(1362668400,1392668400)));

print "==================== Sphinx Serarch Result ============================\n";

$sphinx = new Sphinx();
$res = $sphinx->search($args);
print_r($sphinx);
print_r($res);

