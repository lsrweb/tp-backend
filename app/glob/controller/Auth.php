<?php

namespace app\glob\controller;

use app\glob\Controller;
use app\glob\utils\Captcha;
use cores\Request;
use Firebase\JWT\JWT;
use think\facade\Session;

class Auth extends Controller
{
    protected $jwtKey;
//     初始化,从env中获取jwt的key
    public function initialize()
    {
        $this->jwtKey = env('JWT_KEY');

    }
    /**
     * 登录方法
     *
     * @return json|\think\response\Json
     */
    public function login()
    {
        // 获取账号和密码
        $username = (new \cores\Request)->post('username');
        $password = (new \cores\Request)->post('password');
        $captcha = (new \cores\Request)->post('captcha','ySt3Y');
        halt((new \app\glob\utils\Captcha)->verify($captcha));
        // TODO: 在此处进行账号密码验证，验证通过后生成 token

        // 生成 token
        $payload = [
            'username' => $username,
            'exp' => time() + 3600, // 过期时间为 1 小时后
        ];
        $jwt = $this->issueToken($payload);

        // 返回 token
        return $this->renderJson(200, '登录成功', compact('jwt'));
    }

    /**
     * 验证 token 方法
     *
     * @param string $jwt
     * @return boolean
     */
    public static function verifyToken($jwt)
    {
        try {
            // 解码 token
            $decoded = JWT::decode($jwt, 'my_secret_key', array('HS256'));
            // 验证 token 是否过期
            if ($decoded->exp < time()) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 签发 token 方法
     *
     * @param array $payload
     * @return string
     */
    protected function issueToken($payload)
    {
        return JWT::encode($payload, $this->jwtKey,'HS256');
    }

}