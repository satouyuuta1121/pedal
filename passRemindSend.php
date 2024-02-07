<?php
require("function.php");

debug("パスワード再発行メール送信ページ");
debugLogStart();

    if(!empty($_POST)){
        debug("post通信あり");
        debug("post情報".print_r($_POST,true));

        $email=$_POST["email"];

        validRequired($email,"email");

        if(empty($err_msg)){
            debug("未入力チェックおk");

        validEmail($email,"email");
        validMaxLen($email,"email");

        if(empty($err_msg)){
            debug("バリデおk");

            try{
                $dbh=dbConnect();
                $sql="SELECT count(*) FROM user WHERE email=:email AND delete_flg=0";
                $data=array(":email"=>$email);
                $stmt=queryPost($dbh,$sql,$data);
                $result=$stmt->fetch(PDO::FETCH_ASSOC);
                
                if($stmt && array_shift($result)){
                    debug("クエリ成功　db登録あり");
                    $_SESSION["msg_success"]=SUC03;

                    $auth_key=makeRandKey();

                    $from="info@webukatu.com";
                    $to=$email;
                    $subject="パスワード再発行認証";
                    $comment=<<<EOT
                    本メールアドレス宛にパスワード再発行のご依頼がありました。
                    下記のURLにて認証キーをご入力頂くとパスワードが再発行されます。
                    
                    パスワード再発行認証キー入力ページ：http://localhost:8888/webservice_practice07/passRemindRecieve.php
                    認証キー：{$auth_key}
                    ※認証キーの有効期限は30分となります
                    
                    認証キーを再発行されたい場合は下記ページより再度再発行をお願い致します。
                    http://localhost:8888/webservice_practice07/passRemindSend.php
                    
                    ////////////////////////////////////////
                    ウェブカツマーケットカスタマーセンター
                    URL  http://webukatu.com/
                    E-mail info@webukatu.com
                    ////////////////////////////////////////
                    EOT;
                    sendMail($from,$to,$subject,$comment);

                    $_SESSION["auth_key"]=$auth_key;
                    $_SESSION["auth_email"]=$email;
                    $_SESSION[auth_key_limit]=time()+(60*30);
                    debug("セッション変数の中身".print_r($_SESSION,true));

                    header("Location:passRemindRecieve.php");
                }else{
                    debug("クエリに失敗か登録のないemail");
                    $err_msg["common"]=MSG07;
                }
            }catch(Exception $e){
                error_log("エラー発生".$e->getMessage());
                $err_msg["common"]=MSG07;
            }
        }
    }
}
?>

<?php
$pageTitle="パスワード再発行メール送信";
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
                    <p>ご指定のメールアドレス宛にパスワード再発行用のURLと認証キーをお送り致します</p>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg["common"])) echo $err_msg["common"];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg["email"])) echo "err";?>">
                    email
                    <input type="text" name="email" value="<?php echo getFormData("email");?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg["email"])) echo $err_msg["email"];
                    ?>
                </div>
                <div class="btn-container">
                    <input type="submit" class="btn btn-mid" value="送信する">
                </div>
                </form>
            </div> 
            <a href="login.php">&lt; ログインページに戻る</a>
        </section>
    </div>
    <?php
    require("footer.php");
    ?>