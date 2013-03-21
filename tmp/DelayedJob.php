<?
require_once('rpc/TmonRpcManager.class.php');

class DelayedJob
{
    public function updateJobComplete($filename,$link, $job_id)/*{{{*/
    {
        $updData = $this->getRPC(array("id"=>$job_id,"status"=>"Y","link"=>$link,"filename"=>$filename, "complete_at"=>date('Y-m-d H:i:s')), "DelayedJobs.updateDelayedJob", "array");
    }/*}}}*/

    public function getIngJob($key)/*{{{*/
    {
        $ingJob = $this->getRPC(array("job_key"=>$key), "DelayedJobs.getIngDelayedJob", "array");

        $ingcnt = 0;
        if($ingJob != 'ERROR') {
            $ingcnt = $ingJob['cnt'];
        }

        return $ingcnt;
    }/*}}}*/

    public function getJob($type,$key, $change_status = true)/*{{{*/
    {
        $ingData = $this->getRPC(array("job_type"=>$type,"job_key"=>$key), "DelayedJobs.getDelayedJob", "array");

        $jid = "";
        if($ingData != 'ERROR') {
            $jid = $ingData['id'];
        }

        if($jid) {
            if($change_status == true)
            $updData = $this->getRPC(array("id"=>$jid,"status"=>"G","run_at"=>date('Y-m-d H:i:s')), "DelayedJobs.updateDelayedJob", "array");
            if($updData != 'ERROR') {
                return $ingData;
            }
        }

        exit;

    }/*}}}*/

    public function getJobInfo($key)/*{{{*/
    {
        $ingData = $this->getRPC(array("id"=>$key), "DelayedJobs.getDelayedJobWithId", "array");

        return $ingData;

    }/*}}}*/

    public function completeMail($url, $params)/*{{{*/
    {
        $to = $params['email'];
        $subject = $this->encode($params['created_at']." 에 요청하신 엑셀다운로드 입니다.");

        $message = "요청하신 엑셀 다운로드 입니다.<br/>아래 링크를 클릭하시면 다운로드 할 수 있습니다.<br/><br/><a href='".$url['link']
            ."'>".$url['link']."</a><br/><br/>※ 파일보관기간은 7일입니다. ("
            .date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"))+604800)." 이후 삭제됨)";
        $body = "<html>\n";
        $body .= "<body style=\"font-family:Dotum; font-size:13px; color:#07329b;\">\n";
        $body .= $message;
        $body .= "</body>\n";
        $body .= "</html>\n";

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'From: 예약작업<DelayedJob@tmon.co.kr>' . "\r\n";

        mail($to, $subject, $body, $headers);

        return true;
    }/*}}}*/

    public function failMail($params)/*{{{*/
    {
        $to = $params['email'];
        $subject = $this->encode("엑셀다운로드 실패.");

        $message = "시스템 문제로 인해 요청하신 엑셀 다운로드가 실패되었습니다.<br/>요청하신 작업에 대해서 개발자에게 문의 해주세요.";
        $body = "<html>\n";
        $body .= "<body style=\"font-family:Dotum; font-size:13px; color:#ff5555;\">\n";
        $body .= $message;
        $body .= "</body>\n";
        $body .= "</html>\n";

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'From: 예약작업<DelayedJob@tmon.co.kr>' . "\r\n";

        mail($to, $subject, $body, $headers);

        return true;
    }/*}}}*/

    private function encode($s, $encoding = 'utf-8')/*{{{*/
    {
        return "=?$encoding?B?".base64_encode($s). "?=";
    }/*}}}*/

    private function getRPC($params, $api, $type) {/*{{{*/
        $rpcdata = TmonRpcManager::getData($params, $api, $type, 'itb-rpc');

        return $rpcdata['result'];
    }/*}}}*/
}
