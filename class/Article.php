<?php



require_once  __DIR__."/Error.php";



class Article{

    private $_db;

    public function __construct($_db){
        $this->_db=$_db;
    }



    public function create($title,$content,$user_id){

        if(empty($title)){
            throw  new Exception("标题不能为空",Error::TITLE_CONNOT_NULL);
        }
        if(empty($content)){
            throw  new Exception("内容不能为空",Error::CONTENT_CONNOT_NULL);
        }

        $sql = "insert into `article`(`title`,`content`,`user_id`,`create_time`) values(:title,:content,:user_id,:create_time)";
        $time = date("Y-m-d H:i:s",time());
        $sm =$this->_db->prepare($sql);

        $sm->bindParam(':title',$title);
        $sm->bindParam(':content',$content);
        $sm->bindParam(':user_id',$user_id);
        $sm->bindParam(':create_time',$time);


        if(!$sm->execute()){
            throw  new Exception("文章创建失败",Error::CREATE_ARTICLE_FAIL);
        }

        return [
            "title"=>$title,
            "content"=>$content,
            "addtime"=>$time,
            "acticle_id"=>$this->_db->lastInsertId(),
            "user_id"=>$user_id
        ];

    }


    public function view($t_id){
        if(empty($t_id)){
            throw  new Exception("文章id不能为空",Error::TITLE_ID_CONNOT_NULL);
        }

        $sql = "select * from `article` where `id`= :t_id";

        $sm =$this->_db->prepare($sql);

        $sm->bindParam(':t_id',$t_id);

        if(!$sm->execute()){
            throw  new Exception("获取文章失败",Error::GET_ARTICLE_ERROR);
        }

        $article = $sm->fetch(PDO::FETCH_ASSOC);


        if(empty($article)){
            throw  new Exception("文章跑到火星去了",Error::ARTICLE_NOT_EXISTS);
        }

        return $article;
    }



    public function edit($tid,$title,$content,$user_id){

        if(empty($tid)){
            throw  new Exception("文章id不能为空",Error::TITLE_ID_CONNOT_NULL);
        }

        $article = $this->view($tid);

        if($user_id!=$article["user_id"]){
            throw  new Exception("你没有权限操作",Error::PERMISSION_NOT_ALLOW);
        }

        $title = empty($title)?$article['title']:$title;
        $content = empty($content)?$article['content']:$content;
//        dd($article["title"]);

        if($title == $article['title'] && $content==$article['content'] ){
            return $article;
        }

        $sql = "update `article` set `title`=:title,`content`=:content where `id`=:id";



        $sm =$this->_db->prepare($sql);

        $sm->bindParam(':title',$title);
        $sm->bindParam(':content',$content);
        $sm->bindParam(':id',$tid);

        if(!$sm->execute()){
            throw  new Exception("操作失败",Error::EDIT_ATICLE_FAIL);
        }


        return [
            "title"=>$title,
            "content"=>$content,
            "acticle_id"=>$tid,
            "user_id"=>$user_id
        ];


    }


    public function delete($tid,$user_id){
        $article = $this->view($tid);
        if($user_id!=$article["user_id"]){
            throw  new Exception("你没有权限操作",Error::PERMISSION_NOT_ALLOW);
        }
    }



    public function _list(){

    }


}