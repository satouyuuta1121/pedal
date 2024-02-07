<?php

require("function.php");

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug("ログインページ");
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require("auth.php");

if(!empty($_POST)){
    debug("post通信あり");

    $email=$_POST["email"];
    $pass=$_POST["pass"];
    $pass_save=(!empty($_POST["pass_save"]))? true:false;

    validEmail($email,"email");
    validMaxLen($email,"email");

    validHalf($pass,"pass");
    validMaxLen($pass,"pass");
    validMinLen($pass,"pass");

    validRequired($email,"email");
    validRequired($pass,"pass");

    if(empty($err_msg)){
        debug("バリデok");
        try{
            $dbh=dbConnect();
            $sql="SELECT pass ,id FROM user WHERE email=:email AND delete_flg=0";
            $data=array(":email"=>$email);
            $stmt=queryPost($dbh,$sql,$data);
            $result=$stmt->fetch(PDO::FETCH_ASSOC);
            debug("クエリの結果".print_r($result,true));

            if(!empty($result)&&password_verify($pass,array_shift($result))){
                debug("passマッチした");

                $sesLimit=60*60;
                $_SESSION["login_date"]=time();

                if($pass_save){
                    debug("ログイン保持にする");
                    $_SESSION["login_limit"]=$sesLimit*24*30;
                }else{
                    debug("ログイン保持しない");
                    $_SESSION["login_limit"]=$sesLimit;
                }
                $_SESSION["user_id"]=$result["id"];
                debug("セッションの中身".print_r($_SESSION,true));
                debug("マイページへ遷移");
                header("Location:mypage.php");
            }else{
                debug("passアンマッチ");
                $err_msg["common"]=MSG09;
            }
        }catch(Exception $e){
            error_log("エラー発生".$e->getMessage());
            $err_msg["common"]=MSG07;
        }
    }
}
debug("画面表示処理終了");
?>

<?php
$pageTitle="ログイン";
require("head.php");
?>

    <body class="page-login page-1colum">
        <?php
        require("header.php");
        ?>
        <p id="js-show-msg" style="display:none;" class="msg-slide">
        <?php echo getSessionFlash("msg_success");?>
        </p>
        <div id="contents" class="site-width">
            <section id="main">
                <div class="form-container">
                    <form action="" method="post" class="form">
                        <h2 class="title">ログイン</h2>
                        <div class="area-msg">
                            <?php
                            if(!empty($err_msg["common"]))echo $err_msg["common"];
                            ?>
                        </div>
                        <label class="<?php if(!empty($err_msg["email"]))echo "err";?>">
                        メールアドレス
                        <input type="text" name="email" value="<?php if(!empty($_POST["email"]))echo $_POST["email"];?>">
                        </label>
                        <div class="area-msg">
                            <?php
                            if(!empty($err_msg["email"])) echo $err_msg["email"];
                            ?>
                        </div>
                        <label class="<?php if(!empty($err_msg["pass"]))echo "err";?>">
                        パスワード
                        <input type="password" name="pass" class="js-password" value="<?php if(!empty($_POST["pass"]))echo $_POST["pass"];?>">
                        </label>
                        <div class="area-msg">
                            <?php
                            if(!empty($err_msg["pass"])) echo $err_msg["pass"];
                            ?>
                        </div>
                        <label for="">
                            <input type="checkbox" name="pass_save">次回ログイン省略
                            <input type="checkbox" class="js-password-check">文字を表示する
                        </label>
                        <div class="btn-container">
                            <input type="submit" class="btn btn-mid" value="ログイン">
                        </div>
                        パスワードを忘れた方は<a class="kotira"href="passRemindSend.php">コチラ</a>
                    </form>
                </div>
            </section>
        </div>
    <?php
    require("footer.php");
    ?>
    <script
          src="https://code.jquery.com/jquery-3.2.1.min.js"
          integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
          crossorigin="anonymous"></script>
          <script src="app.js"></script>