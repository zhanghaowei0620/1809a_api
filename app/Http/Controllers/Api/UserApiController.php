<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Model\UserModel;
use Illuminate\Support\Str;

class UserApiController extends Controller
{
    /**获取用户信息*/
    public function userInfo(Request $request){
        $uid = $request->input('uid');
        $userInfo = DB::table('user_info')->where('uid',$uid)->first();
        //var_dump($userInfo);
        $data = [];
        if($userInfo){
            $data['error'] = 0;
            $data['msg'] = 'ok';
        }else{
            $data['error'] = 50001;
            $data['msg'] = 'error';
        }
        die(json_encode($data));
    }
    /**注册*/
    public function register(Request $request){
        //$data = file_get_contents("php://input");   //json字符串数据
        $name = $request->input('name');
        $email = $request->input('email');
        $pass1 = $request->input('pass1');
        $pass2 = $request->input('pass2');
        if($pass1 != $pass2){
            $response = [
                'error' => 50001,
                'msg'   => '两次输入密码不同'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        $e = UserModel::where('email',$email)->first();
        if($e){
            $response = [
                'error' => 50002,
                'msg'   => '该邮箱已被注册'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        $n = UserModel::where('name',$name)->first();
        if($n){
            $response = [
                'error' => 50003,
                'msg'   => '该用户名已被注册'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }

        $pass = password_hash($pass1,PASSWORD_BCRYPT);
        //var_dump($hash);
        $data = [
            'name'=>$name,
            'email'=>$email,
            'password'=>$pass,
            'add_time'=>time()
        ];
        $res = UserModel::insertGetId($data);
        if($res){
            $response = [
                'error'=>0,
                'msg'=>'ok'
            ];
        }
        die(json_encode($response,JSON_UNESCAPED_UNICODE));


    }
    /**登陆*/
    public function login(Request $request){
        $email = $request->input('email');
        $password = $request->input('password');
        $passInfo = DB::table('user_info')->where('email',$email)->first();
        //var_dump($passInfo);exit;
        if(empty($email)){
            $response = [
                'error'=>50005,
                'msg'=>'请输入正确的email或密码'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        if(empty($password)){
            $response = [
                'error'=>50005,
                'msg'=>'请输入正确的email或密码'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        if(empty($passInfo)){
            $response = [
                'error'=>50005,
                'msg'=>'请输入正确的email或密码'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        $pass = $passInfo->password;
        $uid = $passInfo->uid;
        //var_dump($pass);exit;
        $user_pass = password_verify($password,$pass);
        //var_dump($user_pass);exit;
        if($user_pass == false){
            $response = [
                'error'=>50005,
                'msg'=>'请输入正确的email或密码'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }else{
            $token = sha1(Str::random(10).md5(time()).$uid);
            $response = [
                'error'=>0,
                'msg'=>'登陆成功',
                'token'=>$token
            ];

            $id = Redis::incr('id');
            $hsetkey = "id_{$id}";
            $keylist = "H:user_login";
            Redis::hset($hsetkey,'id',$id);
            Redis::hset($hsetkey,'user_id',$uid);
            Redis::hset($hsetkey,'token',$token);
            Redis::hset($hsetkey,'createtime',time());
            Redis::lpush($keylist,$hsetkey);


            //var_dump($token);exit;

            if($email=='zhaoda@qq.com' && $password==$user_pass){
                setcookie('token',$token,time()+86400,'/','1809a_api.com',false,true);
                setcookie('uid',$uid,time()+86400,'/','1809a_api.com',false,true);
            }

        }


        die(json_encode($response,JSON_UNESCAPED_UNICODE));

        //var_dump($data);exit;

    }

    /**用户中心*/
    public function userCenter(){
        echo 1111;
    }





    /**base64加密*/
    public function base64(){
        $arr = "hello 王瘪犊子";
        $base64_data = base64_encode($arr);
        var_dump($base64_data);
    }
    /**base64解密*/
    public function testBase64(Request $request){
        $base64 = $request->input('b64');
        $base64_str = base64_decode($base64,JSON_UNESCAPED_UNICODE);
        var_dump($base64_str);
    }
}
