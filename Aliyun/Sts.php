<?php
/**
 * Created by PhpStorm.
 * User: dxcweb
 * Date: 2019/1/31
 * Time: 下午3:22
 */

namespace LumenTool\Aliyun;


use Aliyun\Api\Sts\Request\V20150401\AssumeRoleRequest;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;

class Sts
{
    private $accessKeyId;
    private $accessKeySecret;
    private $roleArn;

    public function __construct($accessKeyId = null, $accessKeySecret = null, $roleArn = null)
    {
        if (empty($accessKeyId)) {
            $this->accessKeyId = env('STS_ACCESS_KEY_ID');
        } else {
            $this->accessKeyId = $accessKeyId;
        }

        if (empty($accessKeySecret)) {
            $this->accessKeySecret = env('STS_ACCESS_KEY_SECRET');
        } else {
            $this->accessKeySecret = $accessKeySecret;
        }
        if (empty($roleArn)) {
            $this->roleArn = env('ROLE_ARN');
        } else {
            $this->roleArn = $roleArn;
        }
        if (empty($this->accessKeyId) || empty($this->accessKeySecret) || empty($this->roleArn)) {
            dd('sts缺少ACCESS_KEY配置');
        }
    }

    public function ossUpload()
    {
        $policy = '{
    "Statement": [
        {
            "Action": [
                "oss:PutObject",
                "oss:AbortMultipartUpload"
            ],
            "Effect": "Allow",
            "Resource": "acs:oss:*:*:zsy-oss*"
        }
    ],
    "Version": "1"
}';
        return $this->get($policy);
    }

    public function get($policy)
    {
        $domain = 'sts.aliyuncs.com';
        $product = 'Sts';
        $iClientProfile = DefaultProfile::getProfile("cn-hangzhou", $this->accessKeyId, $this->accessKeySecret);
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
        $client = new DefaultAcsClient($iClientProfile);


        $request = new AssumeRoleRequest();
        $request->setRoleSessionName("client_name");
        $request->setRoleArn($this->roleArn);
        $request->setPolicy($policy);
        $request->setDurationSeconds(900);
        $content = $client->getAcsResponse($request);


        if (!empty($content->Credentials)) {
            $rows['AccessKeyId'] = $content->Credentials->AccessKeyId;
            $rows['AccessKeySecret'] = $content->Credentials->AccessKeySecret;
            $rows['Expiration'] = $content->Credentials->Expiration;
            $rows['SecurityToken'] = $content->Credentials->SecurityToken;
        } else {
            $rows['AccessKeyId'] = "";
            $rows['AccessKeySecret'] = "";
            $rows['Expiration'] = "";
            $rows['SecurityToken'] = "";
        }
        return $rows;
    }
}