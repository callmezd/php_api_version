<?php
/*
 *
 * */

require_once  __DIR__."/Error.php";

class User{

    private $_db;

    public function __construct($_db) {
        $this->_db=$_db;
    }

    /**
     * @param mixed $db
     */
    public function register($username,$password,$token)
    {
        if(empty($username)){
            throw  new Exception("用户名不能为空",Error::USERNAME_CONNOT_NULL);
        }
        if(empty($password)){
            throw  new Exception("密码不能为空",Error::PASSWORD_CONNOT_NULL);
        }
        if($this->_isUsernameExits($username)){
            throw  new Exception("用户名已经存在2",Error::USERNAME_EXISTS);
        }
        $sql = "insert into `user`(`name`,`password`,`create_time`,`token`) values(:username,:password,:addtime,:token)";
        $addtime = date("Y-m-d H:i:s",time());
        $password = $this->_md5($password);
        $sm =$this->_db->prepare($sql);
        $sm->bindParam(':username',$username);
        $sm->bindParam(':password',$password);
        $sm->bindParam(':addtime',$addtime);
        $sm->bindParam(':token',$token);

//        dd($password);
        if(!$sm->execute()){
            throw  new Exception("注册失败",Error::REGISTIER_FAIL);
        }
        return [
            "username"=>$username,
            "password"=>$password,
            "user_id"=>$this->_db->lastInsertId(),
            "add"=>$addtime
        ];
    }

    public function login($username,$password){
        if(empty($username)){
            throw  new Exception("用户名不能为空",Error::USERNAME_CONNOT_NULL);
        }
        if(empty($password)){
            throw  new Exception("密码不能为空",Error::PASSWORD_CONNOT_NULL);
        }
        $sql = "select * from `user` where `name`= :username and `password`= :password";
        $sm =$this->_db->prepare($sql);
        $sm->bindParam(':username',$username);
        $sm->bindParam(':password',$this->_md5($password));

        if(!$sm->execute()){
            throw  new Exception("登陆失败",Error::LOGIN_FAIL);
        }
        $res = $sm->fetch(PDO::FETCH_ASSOC);


        if(!$res){
            throw  new Exception("用户名或者密码错误",Error::USERNAME_OR_PASSWORD_ERROR);
        }
        return[
            "username"=>$username,
            "password"=>$password,
            "user_id"=>$res->id,
            "token"=>session_id()
        ];
    }


    public function addToken($id,$token)
    {
        $sql = "update `user` set `token`=:token where `id`=:id";
        $sm =$this->_db->prepare($sql);
        $sm->bindParam(':id',$id);
        $sm->bindParam(':$token',$token);
        $sm->execute();
    }




        private  function _isUsernameExits($username){
        $sql = "select * from `user` where `name`= :username";
        $sm = $this->_db->prepare($sql);
        $sm->bindParam(':username',$username);
        $sm->execute();
        $res = $sm->fetch(PDO::FETCH_ASSOC);
        return !empty($res);
    }

    private  function _md5($password)
    {
        return md5($password, SALT);
    }
}