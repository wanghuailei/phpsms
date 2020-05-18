<?php

namespace Toplan\PhpSms;

use REST;

/**
 * 领先互联
 * http://www.bjlxhl.com/
 * Class LingXianAgent
 *
 * @property string $serverIP
 * @property string $serverPort
 * @property string $accountSid
 * @property string $accountToken
 * @property string $appId
 * @property int    $playTimes
 * @property string $displayNum
 */
class LingXianAgent extends Agent implements ContentSms, VoiceCode
{
    protected static $sendUrl = 'http://101.200.29.88:8082/SendMT/SendMessage';
    protected static $searchUrl = 'http://101.200.29.88:8082/SendMT/SearchStatus';

    public function sendContentSms($to, $content)
    {
        $params = $this->params([
            'CorpID'  => $this->userName,
            'Pwd'     => $this->userPasswd,
            'Cell'    => $this->smsCell,
            'Mobile'  => $to,
            'Content' => $content,
        ]);
        $url    = isset($this->sendUrl) ? $this->sendUrl : self::$sendUrl;
        $result = $this->curlPost($url, $params);
        $this->setResult($result);
    }

    /**
     * 查询短信余额
     * @param  [type] $username [description]
     * @param  [type] $pwd      [description]
     * @return [type]           [description]
     */
    public function getResidueSms()
    {
        $url    = isset($this->searchUrl) ? $this->searchUrl : self::$searchUrl;
        $params = 'username=' . $this->userName . '&password=' . $this->userPasswd;
        $this->curlPost($url, $params);
    }

    /**
     * 预留语音验证码
     * @param array|string $to
     * @param int|string   $code
     */
    public function sendVoiceCode($to, $code) {}

    protected function setResult($result)
    {

//        if(strlen($result)>4){
//            $code = explode(",",$result);
//            if('00' == $code[0]){
//                $msg = ['code'=>$code[0],'info'=>config('smserror.00')];
//            }
//            if('03' == $code[0]){
//                $msg = ['code'=>$code[0],'info'=>config('smserror.03')];
//            }
//            $sendSms['code'] = $code[0];
//        }

        if ($result['request']) {
            $this->result(Agent::INFO, $result['response']);
            $result = json_decode($result['response'], true);
            if ($result['status'] === 'success') {
                $this->result(Agent::SUCCESS, true);
            } else {
                $this->result(Agent::INFO, $result['msg']);
                $this->result(Agent::CODE, $result['code']);
            }
        } else {
            $this->result(Agent::INFO, 'request failed');
        }
    }
}
