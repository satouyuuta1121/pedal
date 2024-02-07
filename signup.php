<?php

require("function.php");

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug("ユーザー登録ページ");
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


if(!empty($_POST)){

    $username=$_POST["username"];
    $email=$_POST["email"];
    $pass=$_POST["pass"];
    $pass_re=$_POST["pass_re"];

    validRequired($username,"username");
    validRequired($email,"email");
    validRequired($pass,"pass");
    validRequired($pass_re,"pass_re");

    if(empty($err_msg)){

        validMaxLen($username,"username");
        validEmail($email,"email");
        validMaxLen($email,"email");
        validEmailDup($email);

        validHalf($pass,"pass");
        validMaxLen($pass,"pass");
        validMinLen($pass,"pass");

        if(empty($err_msg)){
            validMatch($pass,$pass_re,"pass_re");

            if(empty($err_msg)){
                try{
                $dbh=dbConnect();
                $sql="INSERT INTO user (username,email,pass,login_time,create_date)VALUES(:username,:email,:pass,:login_time,:create_date)";
                $data=array(":username"=>$username,":email"=>$email,":pass"=>password_hash($pass,PASSWORD_DEFAULT),
                    ":login_time"=>date("Y-m-d H:i:s"),
                    ":create_date"=>date("Y-m-d H:i:s"));
                $stmt=queryPost($dbh,$sql,$data);
                if($stmt){
                    $sesLimit=60*60;
                    $_SESSION["login_date"]=time();
                    $_SESSION["login_limit"]=$sesLimit;
                    $_SESSION["user_id"]=$dbh->lastInsertId();
                    debug("セッションの中身".print_r($_SESSION,true));
                    header("Location:mypage.php");
                }
                
            }catch(Exception $e){
                error_log("エラー発生".$e->getMessage());
                $err_msg["common"]=MSG07;
            }
        }
        }
    }
}
?>
<?php
$pageTitle="ユーザー登録";
require("head.php");
?>
    <body class="page-signup page-1colum">
        <?php
        require("header.php");
        ?>
        <div id="contents" class="site-width">
            <section id="main">
                <div class="form-container">
                    <form action="" method="post" class="form">
                        <h2 class="title">ユーザー登録</h2>
                        <div class="area-msg">
                            <?php
                            if(!empty($err_msg["common"])) echo $err_msg["common"];
                            ?>
                        </div>
                        <label for="" class="<?php if(!empty($err_msg["username"])) echo "err";?>">
                        ユーザーネーム
                        <input type="text" name="username" value="<?php if(!empty($_POST["username"])) echo $_POST["username"];?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg["username"])) echo $err_msg["username"];
                        ?>
                    </div>
                        <label for="" class="<?php if(!empty($err_msg["email"])) echo "err";?>">
                        email
                        <input type="text" name="email" value="<?php if(!empty($_POST["email"])) echo $_POST["email"];?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg["email"])) echo $err_msg["email"];
                        ?>
                    </div>
                    <label for="" class="<?php if(!empty($err_msg["pass"])) echo "err";?>">
                        パスワード　<span style="font-size:12px;">*英数字6文字以上</span>
                        <input type="password" name="pass" value="<?php if(!empty($_POST["pass"])) echo $_POST["pass"];?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg["pass"])) echo $err_msg["pass"];
                        ?>
                    </div>
                    <label for="" class="<?php if(!empty($err_msg["pass_re"])) echo "err";?>">
                        パスワード（再入力）
                        <input type="password" name="pass_re" value="<?php if(!empty($_POST["pass_re"])) echo $_POST["pass_re"];?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg["pass_re"])) echo $err_msg["pass_re"];
                        ?>
                    </div>
                    <div class="btn-container">
                        <input type="submit" class="btn btn-mid" value="登録する">
                    </div>
                    </form>
                </div>
            </section>
        </div>
    <?php
    require("footer.php");
    ?>
  