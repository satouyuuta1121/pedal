<?php

require("function.php");

debug("退会ページ");
debugLogStart();

require("auth.php");

if(!empty($_POST)){
    debug("post通信あり");
    try{
        $dbh=dbConnect();
        $sql="UPDATE user SET delete_flg=1 WHERE id=:us_id";
        $data=array("us_id"=>$_SESSION["user_id"]);
        $stmt=queryPost($dbh,$sql,$data);

        if($stmt){
            session_destroy();
            debug("セッション変数の中身".print_r($_SESSION,true));
            debug("homeへ遷移");
            header("Location:home.php");
        }else{
            debug("クエリ失敗");
            $err_msg["common"]=MSG07;
        }
    }catch(Exception $e){
        error_log("エラー発生".$e->getMessage);
        $err_msg["common"]=MSG07;
    }
}
debug("画面表示処理完了");
?>
<?php
$pageTitle="退会";
require("head.php");
?>

<body class="page-withdraw page-1colum">
    <style>
        .form .btn{
            float:none;
        }
        .form{
            text-align:center;
        }
    </style>
    
    <?php
    require("header.php");
    ?>

    <div id="contents" class="site-width">
        <section id="main">
            <div class="form-container">
                <form action="" method="post" class="form">
                    <h2 class="title">退会</h2>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg["common"])) echo $err_msg["common"];
                        ?>
                    </div>
                    <div class="btn-container">
                        <input type="submit" class="btn btn-mid" value="退会する" name="submit">
                    </div>
                </form>
            </div>
            <a href="home.php">&lt; マイページに戻る</a>
        </section>
    </div>
    
    <?php
    require("footer.php");
    ?>