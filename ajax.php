<?php
require("function.php");

debug("ajax");
debugLogStart();

if(isset($_POST[productId]) && isset($_SESSION["user_id"]) && isLogin()){
    debug("post通信あり");
    $p_id=$_POST["productId"];
    debug("商品id".$p_id);

    try{
        $dbh=dbConnect();
        $sql="SELECT * FROM `like` WHERE product=:product AND user=:user";
        $data=array(":product"=>$p_id,"user"=>$_SESSION["user_id"]);
        $stmt=queryPost($dbh,$sql,$data);
        $resultCount=$stmt->rowCount();
        debug($resultCount);

        if(!empty($resultCount)){
            $sql="DELETE FROM `like` WHERE product=:product AND user=:user";
            $data=array(":product"=>$p_id,"user"=>$_SESSION["user_id"]);
            $stmt=queryPost($dbh,$sql,$data);
        }else{
            $sql="INSERT INTO `like` (product,user,create_date) VALUES (:product,:user,:date)";
            $data=array(":user"=>$_SESSION["user_id"],":product"=>$p_id,":date"=>date("Y-m-d H:i:s"));
            $stmt=queryPost($dbh,$sql,$data);
        }
    }catch(Exception $e){
        error_log("エラー発生:".$e->getMessage);
    }
}
debug("ajax処理終了");
?>