<?php

//ログ
ini_set("log_errors","on");
ini_set("error_log","php.log");

//デバック
$debug_flg=true;
function debug ($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log("デバック".$str);
    }
}

//セッション
session_save_path("/var/tmp/");
ini_set("session.gc_maxlifetime",60*60*24*30);
ini_set("session.cookie_lifetime",60*60*24*30);
session_start();
session_regenerate_id();

//デバック
function debugLogStart(){
    debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
    debug('セッションID:'.session_id());
    debug('セッション変数の中身：'.print_r($_SESSION,true));
    debug('現在日時タイムスタンプ：'.time());
    if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
    debug( 'ログイン期限日時タイムスタンプ：'.( $_SESSION['login_date'] + $_SESSION['login_limit'] ) );
  }
}

//定数
define("MSG01","入力必須");
define("MSG02","emailの形式で頼む");
define("MSG03","パスワード（再入力）が違う");
define("MSG04","半角英数字のみ");
define("MSG05","6文字以上");
define("MSG06","255文字以内");
define("MSG07","エラー発生。ちょっと待って");
define("MSG08","そのemail登録済み");
define("MSG09","メアドかpassが違う");
define('MSG12', '古いパスワードが違います');
define('MSG13', '古いパスワードと同じです');
define("MSG15","正しくありません");
define("MSG16","有効期限切れ　再発行してください");
define("SUC01","パスワード変更できた");
define("SUC02","プロフィール変更できた");
define('SUC03', 'メールを送信しました');
define('SUC04', '登録しました');

//グローバル変数
$err_msg=array();

//バリデーション
function validRequired($str,$key){
    if($str===""){
        global $err_msg;
        $err_msg[$key]=MSG01;
    }
}

function validEmail($str,$key){
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
        global $err_msg;
        $err_msg[$key]=MSG02;
    }
}

function validEmailDup($email){
    global $err_msg;
    try{
        $dbh=dbConnect();
        $sql="SELECT count(*) FROM user WHERE email=:email AND delete_flg=0";
        $data=array(":email"=>$email);

        $stmt=queryPost($dbh,$sql,$data);

        $result=$stmt->fetch(PDO::FETCH_ASSOC);

        if(!empty(array_shift($result))){
            $err_msg["email"]=MSG08;
        }
    }catch(Exception $e){
        error_log("エラー発生".$e->getMessage());
        $err_msg["common"]=MSG07;
    }
}

function validMatch($str1,$str2,$key){
    if($str1 !== $str2){
        global $err_msg;
        $err_msg[$key]=MSG03;
    }
}

function validMinLen($str,$key,$min=6){
    if(mb_strlen($str)<$min){
        global $err_msg;
        $err_msg[$key]=MSG05;
    }
}

function validMaxLen($str,$key,$max=255){
    if(mb_strlen($str)>$max){
        global $err_msg;
        $err_msg[$key]=MSG06;
    }
}

function validHalf($str,$key){
    if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key]=MSG04;
    }
}

function validPass($str,$key){
    validHalf($str,$key);
    validMaxLen($str,$key);
    validMinLen($str,$key);
}

function validSelect($str,$key){
    if(!preg_match("/^[0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key]=MSG15;
    }
}
function getErrMsg($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        return $err_msg[$key];
    }
}

//ログイン
function isLogin(){
    if(!empty($_SESSION["login_date"])){
        debug("ログイン済みユーザー");

        if(($_SESSION["login_date"]+$_SESSION["login_limit"])<time()){
            debug("ログイン期限オーバー");

            session_destroy();
            return false;
        }else{
            debug("ログイン期限内");
            return true;
        }
    }else{
        debug("未ログインユーザー");
        return false;
    }
}

//データベース
function dbConnect(){
    $dsn="mysql:dbname=pedal;host=localhost;charset=utf8";
    $user="root";
    $password="root";
    $options=array(
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_SILENT,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY=>true,
    );
    $dbh=new PDO($dsn,$user,$password,$options);
    return $dbh;
}
function queryPost($dbh,$sql,$data){
    $stmt=$dbh->prepare($sql);
    if(!$stmt->execute($data)){
        debug("クエリ失敗");
        debug("失敗したsql".print_r($stmt,true));
        $err_msg["common"]=MSG07;
        return 0;
    }
    debug("クエリ成功");
    return $stmt;
}

function getUser($u_id){
    debug("ユーザー情報取得");
    try{
        $dbh=dbConnect();
        $sql="SELECT * FROM user WHERE id = :u_id AND delete_flg=0";
        $data=array(":u_id"=>$u_id);
        $stmt=queryPost($dbh,$sql,$data);

        
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log("エラー発生".$e->getMessage());
    }
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getProduct($u_id,$p_id){
    debug("商品情報取得");
    debug("ユーザーid".$u_id);
    debug("商品id".$p_id);
    try{
        $dbh=dbConnect();
        $sql="SELECT * FROM product WHERE user=:u_id AND id=:p_id AND delete_flg=0";
        $data=array(":u_id"=>$u_id,":p_id"=>$p_id);
        $stmt=queryPost($dbh,$sql,$data);

        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log("エラー発生" .$e->getMessage());
    }
}


function getProductList($currentMinNum = 1, $category, $sort, $span = 20){
    debug('商品情報を取得します。');
    try {
      
      $dbh = dbConnect();
      $sql = 'SELECT id FROM product';
      if(!empty($category)) $sql .= ' WHERE category = '.$category;
      if(!empty($sort)){
        switch($sort){
          case 1:
            $sql .= ' ORDER BY weight ASC';
            break;
          case 2:
            $sql .= ' ORDER BY weight DESC';
            break;
        }
      } 
      
      $data=array();
      $stmt=queryPost($dbh,$sql,$data);
      $rst['total'] = $stmt->rowCount(); 
      //$rst['total_page'] = ceil($rst['total']/$span); 
      if(!$stmt){
        return false;
      }

      $sql = 'SELECT * FROM product';
      if(!empty($category)) $sql .= ' WHERE category = '.$category;
      if(!empty($sort)){
        switch($sort){
          case 1:
            $sql .= ' ORDER BY weight ASC';
            break;
          case 2:
            $sql .= ' ORDER BY weight DESC';
            break;
        }
      } 
     
      $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
      $data = array();
      debug('SQL：'.$sql);
      
      $stmt = queryPost($dbh, $sql, $data);
  
      if($stmt){
        
        $rst['data'] = $stmt->fetchAll();
        
        return $rst;
      }else{
        return false;
      }
  
    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
    }
  }

function getProductOne($p_id){
    debug("商品情報取得");
    debug("商品id:".$p_id);

    try{
        $dbh=dbConnect();
        $sql="SELECT * FROM product WHERE id=:p_id";
        $data=array(":p_id"=>$p_id);
        $stmt=queryPost($dbh,$sql,$data);
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log("エラー発生".$e->getMessage);
    }
}

function getMyProducts($u_id){
    debug("自分の商品情報取得");
    debug("ユーザーid".$u_id);

    try{
        $dbh=dbConnect();
        $sql="SELECT * FROM product WHERE user_id=:u_id AND delete_flg=0";
        $data=array(":u_id"=>$u_id);
        $stmt=queryPost($dbh,$sql,$data);

        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log("エラー発生".$e->getMessage());
    }
}

function getMessageAndBord($id){
    debug("msg情報取得");
    debug("掲示板id".$id);

    try{
        $bdh=dbConnect();
        $sql='SELECT m.id AS m_id, product_id, bord_id, send_date, to_user, from_user, sale_user, buy_user, msg, b.create_date FROM message AS m RIGHT JOIN bord AS b ON b.id = m.bord_id WHERE b.id = :id AND ORDER BY send_date ASC';
        $data=array(":id"=>$id);
        $stmt=queryPost($dbh,$sql,$data);

        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log("エラー発生".$e->getMessage());
    }
}

/*function getMsgAndBord($u_id);
debug("自分のmsgを取得");

try{
    $dbh=dbConnect();
    $sql="SELECT * FROM bord AS b WHERE b.sele_user=:id OR b.buy_user=:id AND b.deleteflg=0";
    $data=array(":id"=>$u_id);
    $stmt=queryPost($dbh,$sql,$data);
    $rst=$stmt->fetchAll();
    if(!empty($rst)){
        foreach($rst as $key => $val){
            $sql="SELECT * FROM message WHERE bord_id=:id AND delete_flg=0 ORDER BY send_date DESC";
            $data=array(":id"=>$val["id"]);
            $stmt=queryPost($dbh,$sql,$data);
            $rst[$key]["msg"]=>$stmt->fetchAll();
        }
    }
    if($stmt){
        return $rst;
    }else{
        return false;
    }
    catch(Exception $e){
        error_log("エラー発生".$e->getMessage());
    }
}
*/

function getCategory(){
    debug("カテゴリー情報取得");
    try{
        $dbh=dbConnect();
        $sql="SELECT * FROM category";
        $data=array();
        $stmt=queryPost($dbh,$sql,$data);
        
        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log("エラー発生".$e->getMessage());
    }
}

function isLike($u_id,$p_id){
    debug("お気に入りがあるか確認");
    debug("ユーザーid".$u_id);
    debug("商品id".p_id);

    try{
        $dbh=dbConnect();
        $sql="SELECT * FROM `like` WHERE product=:p_id AND user=:u_id";
        $data=array(":u_id"=>$u_id,":p_id"=>$p_id);
        $stmt=queryPost($dbh,$sql,$data);
        
        /*if($stmt->rowCount()){
            debug("お気に入りです");
            return true;
        }else{
            dubug("気に入ってない");
            return false;
        }*/
    }catch(Exception $e){
        error_log("エラー発生".$e->getMessage());
    }
}

function getMyLike($u_id){
    debug("お気に入り情報取得");
    debug("ユーザーid".$u_id);

    try{
        $dbh=dbConnect();
        $sql="SELECT * FROM `like` AS l LEFT JOIN product AS p ON l.puroduct_id=p_id WHERE l.user_id=:u_id";
        $data=array(":u_id"=>$u_id);
        $stmt=queryPost($dbh,$sql,$data);

        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log("エラー発生".$e->getMessage());
    }
}

//メール送信
function sendMail($from,$to,$subject,$comment){
    if(!empty($to) && !empty($subject) && !empty($comment)){
        mb_language("japanese");
        mb_internal_encoding("UTF-8");
        $result=mb_send_mail($to,$subject,$comment,"From:".$from);
        if($result){
            debug("メール送信した");
        }else{
            debug("メール送信失敗");
        }
    }
}

//その他
function sanitize($str){
    return htmlspecialchars($str,ENT_QUOTES);
}

function getFormData($str,$flg=false){
    if($flg){
        $method=$_GET;
    }else{
        $method=$_POST;
    }
    global $dbFormData;
    if(!empty($dbFormData)){
        if(!empty($err_msg[$str])){
            if(isset($_POST[$str])){
                return $method[$str];
            }else{
                return $dbFormData[$str];
            }
        }else{
            if(isset($method[$str])&& $method[$str]!==$dbFormData[$str]){
                return $method[$str];
            }else{
                return $dbFormData[$str];
            }
        }
    }else{
        if(isset($method[$str])){
            return $method[$str];
        }
    }
}


function getSessionFlash($key){
    if(!empty($_SESSION[$key])){
        $data=$_SESSION[$key];
        $_SESSION[$key]="";
        return $data;
    }
}

function makeRandKey($length=6){
    $chars="0";
    $str="";
    for($i=0; $i<$length; ++$i){
        $str .=$chars[mt_rand(0,0)];
    }
    return $str;
}

function uploadImg($file,$key){
    debug("画像アップロード処理開始");
    debug("file情報".print_r($file,true));

    if(isset($file["error"]) && is_int($file["error"])){
        try{
            switch($file["error"]){
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException("ファイルが選択されてない");
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException("ファイルサイズが大きい");
                default:
                    throw new RuntimeException("その他のエラー");
            }
            $type=@exif_imagetype($file["tmp_name"]);
            if (!in_array($type,[IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG],true)){
                throw new RuntimeException("画像形式が未対応");
            }
            $path="uploads/".sha1_file($file["tmp_name"]).image_type_to_extension($type);
            if(!move_uploaded_file($file["tmp_name"],$path)){
                throw new RuntimeException("ファイル保存時にエラー");
            }
            chmod($path,0644);
            debug("ファイルは正常にアップロード");
            debug("ファイルパス".$path);
            return $path;
        }catch(RuntimeException $e){
            debug($e->getMessage());
            global $err_msg;
            $err_msg[$key]=$e->getMessage();
        }
    }
}

function showImg($path){
    if(empty($path)){
        return "img/sample-img.png";
    }else{
        return $path;
    }
}

function appendGetParam($arr_del_key=array()){
    if(!empty($_GET)){
        $str="?";
        foreach($_GET as $key => $val){
            if(!in_array($key,$arr_del_key,true)){
                $str.=$key."=".$val."&";
            }
        }
        $str=mb_substr($str,0,-1,"UTF-8");
        return $str;
    }
}