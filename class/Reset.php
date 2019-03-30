<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/3/24
 * Time: 1:03
 */

class Reset
{
    private $_user;
    private $_article;
    private $_resetMethod;
    private $_resetResource;

    private $allowResource=['users','acticle'];
    private $allowMethod=['GET','POST','DELETE','PUT'];


    private $_resetUrl;
    private $_version;



    private $_status=[
        200=>'OK',
        204=>'NO CONTENT',
        400=>'Bad Requset',
        403=>'Forbidden',
        404=>'not find',
        405=>'not allowed',
        500=>'sever error'
    ];

    public function __construct($_user,$_article)
    {
        $this->_user=$_user;
        $this->_article=$_article;
    }


    public function setMethod()
    {
        $this->_resetMethod=$_SERVER['REQUEST_METHOD'];
        if(!in_array($this->_resetMethod,$this->allowMethod))
        {
            throw  new Exception("请求方法不被允许",Error::METHOD_NOT_ALLOW);
        }
    }


    public function setResource()
    {
        $path=$_SERVER['REDIRECT_PATH_INFO'];
        $params = explode('/',$path);
        $this->_resetResource=$params[1];
        if(!in_array($this->_resetResource,$this->allowResource)){
            throw  new Exception('请求资源不存在',Error::PATH_NOT_FIND);
        }
    }

    public function checkVersion(){
        $path=$_SERVER['REDIRECT_PATH_INFO'];
        $params = explode('/',$path);
        $this->_version=$params[0];
        $this->_resetUrl=$params[2];
        if($this->_version!=VERSION){
            throw  new Exception('api版本不正确',Error::ERROR_VERSION);
        }
    }

    public function _run()
    {
        try{
            $this->setMethod();
            $this->checkVersion();
            $this->setResource();
            if($this->_resetResource == "users"){
                $this->sendUser();
            }
        }catch (Exception $e){
            $this->_json($e->getMessage(),$e->getCode());
        }
    }

    public function _json($message,$code,$data=[])
    {
//        if($code!==200){
//            header('HTTP/1.1 '.$code.' '.$this->_status[$code]);
//        }
        header('Content-Type:application/json;charset=utf-8');
        if(!empty($message)){
            echo  json_encode(['messages'=>$message,'code'=>$code,'data'=>$data]);
        }
        die;
    }

    public function sendUser(){

        if($this->_resetMethod!=="POST"){
            throw  new Exception("请求方法不被允许",Error::METHOD_NOT_ALLOW);
        }
        if($this->_resetUrl=='register'){
            $this->toRegister();
        }

        elseif ($this->_resetUrl=='login'){
            $this->toLogin();

        }
        else{
            throw  new Exception("请求url错误",Error::URL_ERROR);
        }
    }

    public  function toRegister()
{



    $data =  $this->getBody();

    if(empty($data["username"])){
        throw  new Exception("用户名不能为空",400);
    }
    if(empty($data["password"])){
        throw  new Exception("用户密码不能为空",400);
    }
    $user= $this->_user->register($data["username"],$data["password"],$_SERVER['HTTP_ORIGIN']);

    if($user){
        $data=[
            'username'=>$user['username'],
            'id'=>$user['user_id'],
            'token'=> $_SERVER['HTTP_ORIGIN']
        ];

        return $this->_json("注册成功",200,$data);
    }

}

    public  function toLogin()
    {

        $data =  $this->getBody();
//        dd($data);
        if(empty($data["username"])){
            throw  new Exception("用户名不能为空",400);
        }
        if(empty($data["password"])){
            throw  new Exception("用户密码不能为空",400);
        }
        $user= $this->_user->login($data["username"],$data["password"]);
        if($user){
            dd($user);
            return $this->_json("注册成功",200);
        }

    }
    public  function getBody()
    {
        $data =$_POST;
        if(empty($data)){
            throw  new Exception("参数错误",400);
        }
        return $data;




//        $data =file_get_contents("php://input");
//        if(empty($data)){
//            throw  new Exception("参数错误",400);
//        }
//        return json_decode($data,true) ;
    }

}