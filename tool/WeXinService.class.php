<?php

/**
 * Class WeXinService
 * 使用不频繁，所以没有存access_token后续如果有频繁使用在
 */
class WeXinService
{

    private $appid;

    private $appsecret;

    private $access_token;


    public function __construct($appid=WEXIN_SERVICE_APPID, $appsecert=WEIXIN_APPSEECRET)
    {
        $this->appid = $appid;
        $this->appsecret = $appsecert;
        if (!$this->setAccessToken()) {
            Output::fail('获取微信access_token失败');
        }
    }

    private function setAccessToken()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->appsecret;
        $res = file_get_contents($url);
        @$res = json_decode($res, true);
        if (isset($res['access_token'])) {
            $this->access_token = $res['access_token'];
            return true;
        }

        return false;
    }

    public function sedFundMessage($sug, $url=null)
    {
        $data = [
            'touser'        =>  WEIXIN_FUND_TO_USER,
            'template_id'   =>  WEIXIN_FUND_TEMPLATE_ID,
            'topcolor'      =>  '#FF0000',
            'data'          =>  [
                'suggestion'=>  [
                    'value'     =>  $sug,
                    'color'     =>  ''
                ]
            ]
        ];
        if ($url) {
            $data['url'] = $url;
        }
        $this->sendMessage(json_encode($data));
    }

    public function sendMessage($data)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->access_token;

        return curl($url, $data);
    }


}