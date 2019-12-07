<?php

namespace bonza\leads;

use bonza\leads\extend\Curl;
use Exception;
use RuntimeException;

/**
 * Created by yang
 * User: bonzaphp@gmail.com
 * Date: 2019-12-05
 * Time: 11:38
 */
class Tencent
{
    /**
     * API地址
     * @var string
     */
    private $base_url;
    /**
     * @var string
     */
    private $token;
    /**
     * @var string
     */
    private $secret;

    public function __construct(array $options)
    {
        $this->base_url = $options['base_url'] ?? $this->exception('请求地址base_url不存在');
        $this->token = $options['token'] ?? $this->exception('请求token不存在');
        $this->secret = $options['secret'] ?? $this->exception('请求秘钥不存在');
    }

    private function exception(string $msg)
    {
        throw new RuntimeException($msg);
    }

    /**
     * 生成签名
     * @param  string  $timestamp
     * @return string
     * @author bonzaphp@gmail.com
     */
    public function Signature(string $timestamp): string
    {
        $timestamp = $timestamp === '' ? (string) (time()) : $timestamp;
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
            $signature = $this->Signature('');
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

    /**
     * 线索回传接口
     * @param  array  $params = [
     *      'list'=>[],最多同时回传50条线索
     *      'leads_convert_type'=>'常量'
     *      'leads_ineffect_reason' => '常量'
     * ]
     * @return array
     * @author bonzaphp@gmail.com
     */
    public function report(array $params = [])
    {
        try {
            $signature = $this->Signature('');
            $url = $this->base_url.'leads/report';
            $curl = new Curl();
            $url .= '?';
            $url .= http_build_query($params);
            return $curl->sendRequest($url, 'post', $params, [
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