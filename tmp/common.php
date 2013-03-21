<?php 
class Common {	
	/**
	*	티켓번호 리버스(문자열 뒤집고 $seperate에 "-" 추가)
	*
	*	@param array $tickets 티켓번호
	*	@param integer $seperate 구분자 삽입할 문자열 포인터
	*	@return array $ret 리버스한 티켓번호
	*/	
	public function reverseTicket($tickets, $seperate=5) {/*{{{*/
		$ret = array();
        if(is_array($tickets)) {
            $ret = array();
            foreach($tickets as $ticket) {
                $revStr = join("", array_reverse(preg_split("//u", $ticket)));
                array_push($ret, substr(0, strlen($revStr)-$seperate)."-".substr($revStr, $seperate*(-1)));
            }
            return $ret;
        } else {
            $revStr = join("", array_reverse(preg_split("//u", $tickets)));
            return $seperate > 0 ? substr($revStr, 0, strlen($revStr)-$seperate)."-".substr($revStr, $seperate*(-1)) : $revStr;
        }
	}/*}}}*/

	//페이지번호를 제외한 url 리턴
	public function get_url_string()/*{{{*/
	{
		$CI =& get_instance();
		$url = $CI->uri->uri_string();
		$temp = explode("/",$url);

		$baseurl = "";
		
		for($i=0;$i<2;$i++)
		{
			$baseurl .= "/".$temp[$i];
		}

		$segment = $CI->uri->uri_to_assoc(3);
		unset($segment['page']);	//페이지번호만 제외
		
		foreach($segment as $key => $val)
		{
			$baseurl .= "/".$key."/".$val;
		}
		return $baseurl;
	}/*}}}*/

	//페이징
	public function print_page($totalcount=0, $perpage=10, $curpage=1)/*{{{*/
	{
		$baseurl = $this->get_url_string();
		$block_set = 10;
		$totalpage = ceil($totalcount/$perpage);
		$total_block = ceil($totalpage/$block_set);
		$now_block = ceil($curpage/$block_set);
		$n_page = floor(($curpage-1)/$block_set);



		$strHtml = "<div class=\"pagination\"><ul>";

		if($now_block > 1){
			$prev_page = $block_set*($now_block-1)-9;
			$strHtml .= "<li class=\"prev\"><a href=\"".$baseurl."/page/".$prev_page."\">이전페이지</a></li>";
		}
		
		$is = ($n_page * $block_set) + 1;

		for($i=$is; $i < $is+$block_set; $i++){
			if($i <= $totalpage){
				if($i == $curpage){
					$strHtml .= "<li class=\"active\"><a href=\"".$baseurl."/page/".$i."\">".$i."</a></li>";
				}else{
					$strHtml .= "<li><a href=\"".$baseurl."/page/".$i."\">".$i."</a></li>";
				}
			}
		}
		if($now_block < $total_block){
			$next_page = $block_set*$now_block+1;
			$strHtml .= "<li class=\"next\"><a href=\"".$baseurl."/page/".$i."\">다음페이지</a></li>";
		}

		$strHtml .= "</ul></div>";
		return $strHtml;
	}/*}}}*/

	public function print_page2($totalcount=0, $perpage=10, $curpage=1, $method=null)/*{{{*/
	{
		$baseurl = $this->get_url_string();
		$block_set = 10;
		$totalpage = ceil($totalcount/$perpage);
		$total_block = ceil($totalpage/$block_set);
		$now_block = ceil($curpage/$block_set);
		$n_page = floor(($curpage-1)/$block_set);

		$strHtml = "<div class=\"pagination\"><ul>";
		
		if($method)
		{
			if($now_block > 1){
				$prev_page = $block_set*($now_block-1)-9;
				$strHtml .= "<li class=\"prev\"><a href=\"javascript:goPage('".$baseurl."/page/".$prev_page."','".$method."');\">이전페이지</a></li>";
			}
			
			$is = ($n_page * $block_set) + 1;
			
			for($i=$is; $i<$is+$block_set;$i++){
				if($i <= $totalpage){
					if($i == $curpage){
						$strHtml .= "<li class=\"active\"><a href=\"javascript:goPage('".$baseurl."/page/".$i."','".$method."');\">".$i."</a></li>";
					}else{
						$strHtml .= "<li><a href=\"javascript:goPage('".$baseurl."/page/".$i."','".$method."');\">".$i."</a></li>";
					}
				}
			}

			if($now_block < $total_block)
			{
				$next_page = $block_set*$now_block+1;
				$strHtml .= "<li class=\"next\"><a href=\"javascript:goPage('".$baseurl."/page/".$next_page."','".$method."');\">다음페이지</a></li>";
			}
		}
		else
		{
			if($now_block > 1){
				$prev_page = $block_set*($now_block-1)-9;
				$strHtml .= "<li class=\"prev\"><a href=\"javascript:goPage('".$baseurl."/page/".$prev_page."');\">이전페이지</a></li>";
			}
			
			$is = ($n_page * $block_set) + 1;
			
			for($i=$is; $i<$is+$block_set;$i++){
				if($i <= $totalpage){
					if($i == $curpage){
						$strHtml .= "<li class=\"active\"><a href=\"javascript:goPage('".$baseurl."/page/".$i."');\">".$i."</a></li>";
					}else{
						$strHtml .= "<li><a href=\"javascript:goPage('".$baseurl."/page/".$i."');\">".$i."</a></li>";
					}
				}
			}
			
			if($now_block < $total_block){
				$next_page = $block_set*$now_block+1;
				$strHtml .= "<li class=\"next\"><a href=\"javascript:goPage('".$baseurl."/page/".$i."');\">다음페이지</a></li>";
			}
		}
		
		$strHtml .= "</ul></div>";
		return $strHtml;
	}/*}}}*/

	/**
	*	ref_code 가져오기
	*
	*	@param string $ref_field ref_field
	*	@return array $result['result'] 
	*/
	public function getRefcode($ref_field)/*{{{*/
	{
		$CI =& get_instance();
		$CI->load->model('commonmodel','cmodel');

		$result = $CI->cmodel->getRefCode(array('ref_field'=>$ref_field));
		return $result['result'];
	}/*}}}*/
	
	//SMS 발송
	public function sendSMS($params)/*{{{*/
	{
		$CI =& get_instance();
		$CI->load->model('commonmodel','cmodel');

		$result = $CI->cmodel->sendSMS($params);
		return $result;
	}/*}}}*/

    /**
    *   대량 SMS 발송
    *
    *	@param array  "receiver_list"=>발송대상자목록, "template"=>메세지, "force"=>true
    *	@return 
    */

	public function sendSMSMulti($params)/*{{{*/
	{
		$CI =& get_instance();
		$CI->load->model('commonmodel','cmodel');

		$result = $CI->cmodel->sendSmsFromTemplate($params);
		return $result;
	}/*}}}*/

	/**
	*	홍보문구 get
	*
	*	@return array $result
	*/
	public function getPR($params)/*{{{*/
	{
		$CI =& get_instance();
		$CI->load->model('commonmodel','cmodel');

		$result = $CI->cmodel->getPR($params);
		return $result;
	}/*}}}*/

	//DelayedJob
	public function addDelayedJob($params)/*{{{*/
	{
		$CI =& get_instance();
		$CI->load->model('commonmodel','cmodel');

		$result = $CI->cmodel->addDelayedJob($params);
		return $result;
	}/*}}}*/

	/**
	*	get ref_code all
	*	
	*	@param string $ref_field="all"
	*	@return array $aRefcode 
	*/
	public function getRefcodeAll($ref_field="all")/*{{{*/
	{
		$CI =& get_instance();
		$CI->load->model('commonmodel','model');

		$result = $CI->model->getRefCode(array('ref_field'=>$ref_field));
		foreach($result['result'] as $key => $val) {
			$aRefcode[$val['ref_field']][] = $val;
		}
		return $aRefcode;
	}/*}}}*/

	//DEBUG를 위한 xmp
	function xmp($a, $b=NULL)/*{{{*/
	{
		echo "<xmp>";
		if (is_string($a) && $b!==NULL)
		{
			echo "# {$a}: ";
			print_r($b);
		}
		else
			print_r($a);
		echo "</xmp>\n";
	}/*}}}*/

    /**
    *   결과값 Return
    *
    *   @param string $result 결과성공여부
	* 	@param string $sMessage 결과 메시지
    *   @return object json
    */

	function getResult($result, $sMessage)/*{{{*/
	{
		if($result['success'] == 'OK'){
			$success = 'OK';
			$msg = $sMessage;
		}else{
			$success = 'NOK';
			$msg = "처리 실패 : 시스템오류입니다(".$result['error']['message'].")";
		}
		$ret = array(
			"ret"=>$success,
			"msg"=>$msg
		);
		return json_encode($ret);

    }/*}}}*/
    /**
     * 딜 종류 등의 리스트 코멘트 생성 ( ex : (상시/폐쇄)
     *
     * @param   array   $arr
     * @return  string  
     */
    function getDealTypeComment($arr) { /* {{{ */
        $comment = array();
        $result = '';
        if($arr['always_sale'] == 'Y') $comment[] = '상시';
        if($arr['is_hide_deal'] == 'Y') $comment[] = '폐쇄';

        if(count($comment)) $result = "(".implode("/",$comment).")";

        return $result;
    } /* }}} */

    function getRndNumber() {/*{{{*/
        return substr("00000".rand(1, 999999), -6);
    }/*}}}*/

    function checkTmonIP() {/*{{{*/
        $mc = TmonMemcache::instance();
        $ip_cheat = $mc->get('ip_cheat_for_qa');

        if($ip_cheat) {
            return false;
        }

        $tmonIPs = array(
            "10"    //10.x (10.1.x:루터, 10.2.x:시그마)
        );

        $tmonVPNIPs = array(
            "10.1.96",  //Office VPN
            "10.1.97",  //Office VPN
            "10.201.2"  //IDC VPN
        );

        $cip = $_SERVER['REMOTE_ADDR'];
        $arr_cip = explode(".",$cip);
        if(in_array($arr_cip[0].".".$arr_cip[1].".".$arr_cip[2], $tmonVPNIPs)) {
            return false;
        }
        if(in_array($arr_cip[0], $tmonIPs)) {
            return true;
        } else {
            return false;
        }
    }/*}}}*/
}
?>
