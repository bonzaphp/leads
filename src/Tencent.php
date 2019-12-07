<?php

use bonza\leads\extend\Curl;

/**
 * Created by yang
 * User: bonzaphp@gmail.com
 * Date: 2019-12-05
 * Time: 11:38
 */
class Tencent
{
    private $base_url = 'https://leads.qq.com/api/mv1/';
    private $token    = '141292841572938199';
    private $secret   = '8d2cb68398b0d99b1ec803c2f3a88a51';

    public function __construct()
    {
    }

    /**
     * 生成签名
     * @return string
     * @author bonzaphp@gmail.com
     */
    public function Signature(): string
    {
        $timestamp = (string) (time());
        return base64_encode($this->token.','.$timestamp.','.sha1($this->token.'.'.$timestamp.'.'.$this->secret));
    }

    /**
     * 拉取线索信息
     * @param  array  $params
     * @return array
     * @author bonzaphp@gmail.com
     */
    public function getList(array $params = []): array
    {
        try {
            $signature = $this->Signature();
            $url = $this->base_url.'leads/list';
            $curl = new Curl();
            $url .= '?';
            $url .= http_build_query($params);
            return $curl->sendRequest($url, 'get', [], [
                "X-Signature: {$signature}",
                'Accept-Charset: utf-8',
                'Accept: application/json',
                'Content-Type: application/json'
            ]);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }


}