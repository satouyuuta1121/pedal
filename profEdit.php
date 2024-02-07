<?php
require("function.php");

debug("プロフィール編集ページ");
debugLogStart();

require("auth.php");

$dbFormData=getUser($_SESSION["user_id"]);

debug("取得したユーザー情報".print_r($dbFormData,true));

if(!empty($_POST)){
    debug("post通信あり");
    debug("post情報".print_r($_POST,true));
    debug("file情報".print_r($_FILES,true));

    $username=$_POST["username"];
    $email=$_POST["email"];
    $img=(!empty($_FILES["img"]["name"]))?uploadImg($_FILES["img"],"img"):"";
    $img=(empty($img)&&!empty($dbFormData["img"]))?$dbFormData["img"]:$img;

    if($dbFormData["username"]!==$username){
        validMaxLen($username,"username");
    }
    if($dbFormData["email"]!==$email){
        validMaxLen($email,"email");
        if(empty($err_msg["email"])){
            validEmailDup($email);
        }
        validEmail($email,"email");
        validRequired($email,"email");
    }
    if(empty($err_msg)){
        debug("バリデおk");
        try{
            $dbh=dbConnect();
            $sql="UPDATE user SET username=:u_name,email=:email,img=:img WHERE id=:u_id";
            $data=array(":u_name"=>$username,":email"=>$email,":img"=>$img,':u_id'=>$dbFormData["id"]);
            $stmt=queryPost($dbh,$sql,$data);
            if($stmt){
                $_SESSION["msg_success"]=SUC02;
                debug("マイページへ遷移");
                header("Location:mypage.php");
            }
        }catch(Exception $e){
            error_log("エラー発生".$e->getMessage);
            $err_msg["common"]=MSG07;
        }
    }
}
debug("画面表示処理完了");
?>
<?php
$pageTitle="プロフィール編集";
require("head.php");
?>

<body class="page-profEdit page-2colum page-logined">
    <?php
    require("header.php");
    ?>
    <div id="contents" class="site-width">
        <h1 class="page-title">プロフィール編集</h1>
        <section id="main">
            <div class="form-container">
                <form action="" method="post" class="form" enctype="multipart/form-data">
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg["common"])) echo $err_msg["common"];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg["username"])) echo "err";?>">
                    ユーザーネーム
                    <input type="text" name="username" value="<?php echo getFormData("username");?>">
                    </label>
                    <div class="area-msg">
                    <?php
                    if(!empty($err_msg["username"])) echo $err_msg["username"];
                    ?>
                    <label class="<?php if(!empty($err_msg["email"])) echo "err";?>">
                    email
                    <input type="text" name="email" value="<?php echo getFormData("email");?>">
                    </label>
                    <div class="area-msg">
                    <?php
                    if(!empty($err_msg["email"])) echo $err_msg["email"];
                    ?>
                </div>
                プロフィール画像
                <label class="area-drop <?php if(!empty($err_msg["img"])) echo "err";?>" style="height:370px;line-height:370px;">
                <input type="hidden"name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="img" class="input-file" style="height:370px;">
                <img src="<?php echo getFormData("img");?>" alt="" class="prev-img" style="<?php if(empty(getFormData("img"))) echo "display:none;"?>">
                ドラッグ＆ドロップ
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg["img"])) echo $err_msg["img"];
                    ?>
                </div>
                <div class="btn-container">
                    <input type="submit" class="btn btn-mid" value="変更する">
                </div>
                </form>
            </div>
        </section>
        <?php
        require("sidebar.php");
        ?>
    </div>
    <?php
    require("footer.php")
    ?>
