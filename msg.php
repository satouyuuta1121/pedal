<?php
require("function.php");

debug("掲示板");
debugLogStart();

$partnerUserId="";
$partnerUserInfo="";
$myuserId="";
$productInfo="";
$m_id=(!empty($_GET["m_id"]))? $_GET["m_id"]:"";
$viewData=getMsgsAndBord($m_id);
debug("取得したdbデータ".print_r($viewData,true));

if(empty($viewData)){
    error_log("エラー発生:指定ページに不正な値が入りました");
    header("Location:mypage.php");
}
$productInfo=getProductOne($viewData[0]["product_id"]);
debug("取得したdbデータ".print_r($productInfo,true));

if(empty($productInfo)){
    error_log('エラー発生:商品情報が取得できませんでした');
    header("Location:mypage.php");
}
$dealUserIDs[]=$viewData[0]["sale_user"];
$dealUserIDs[]=$viewData[0]["buy_user"];
if(($key=array_search($_SESSION["user_id"],$dealUserIDs))!==false){
    unset($dealUserIDs[$key]);
}
$partnerUserId=array_shift($dealUserIDs);
debug("取得した相手のユーザーid".$partnerUserId);

if(isset($partnerUserId)){
    $partnerUserInfo=getUser($partnerUserId);
}
if(empty($partnerUserInfo)){
    error_log('エラー発生:相手のユーザー情報が取得できませんでした');
    header("Location:mypage.php");
}
$myUserInfo=getUser($_SESSION["user_id"]);
debug('取得したユーザデータ：'.print_r($partnerUserInfo,true));

if(empty($myUserInfo)){
    error_log('エラー発生:自分のユーザー情報が取得できませんでした');
    header("Location:mypage.php");
}

if(!empty($_POST)){
    debug("post通信あり");

    require("auth.php");

    $msg(isset($_POST["msg"]))? $_POST["msg"]:"";
    validMaxLen($msg,"msg",500);
    validRequired($msg,"msg");

    if(empty($err_msg)){
        debug("バリデおk");

        try{
            $dbh=dbConnect();
            $sql = 'INSERT INTO message (bord_id, send_date, to_user, from_user, msg, create_date) VALUES (:b_id, :send_date, :to_user, :from_user, :msg, :date)';
            $data = array(':b_id' => $m_id, ':send_date' => date('Y-m-d H:i:s'), ':to_user' => $partnerUserId, ':from_user' => $_SESSION['user_id'], ':msg' => $msg, ':date' => date('Y-m-d H:i:s'));
            $stmt=queryPost($dbh,$sql,$data);

            if($stmt){
                $_POST=array();
                debug("連絡掲示板へ遷移");
                header("Location:".$_SERVER["PHP_SELF"]."?m_id=".$m_id);
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
$pageTitle="連絡掲示板";
require("head.php");
?>

<body class="page-msg page-1colum">
    <?php
    require("header.php");
    ?>
    <p id="js-show-msg" style="display:none;" class="msg-slide">
    <?php echo getSessionFlash("msg_success");?>
    </p>
    <div id="contents" class="site-width">
        <section id="main">
            <div class="area-bord" id="js-scroll-bottom">
                <?php(!empty($viewData)){
                    foreach($viewData as $key => $val){
                        if(!empty($val["from_user"])&& $val["from_user"]==$partnerUserId){
                            ?>
                            <div class="msg-cnt msg-left">
                                <p class="msg-inrTxt">
                                    <span class="triangle"></span>
                                    <?php echo ($val["msg"]); ?>
                                </p>
                                <div><?php echo ($val["send_date"]);?></div>
                            </div>
                            <?php
                        }else{
                            ?>
                            <div class="msg-cnt msg-right">
                                <p class="msg-inrTxt">
                                    <span class="triangle"></span>
                                    <?php echo ($val["msg"]);?>
                                </p>
                                <div><?php echo ($val["send_date"]);?></div>
                            </div>
                            <?php
                        }
                    }
                }else{
                    ?>
                    <p>メッセージ投稿はまだない</p>
                    <?php
                }
                ?>
            </div>
            <div class=area-send-msg">
            <form action="" method="post">
                <textarea name="mag" id="" cols="30" rows="3"></textarea>
                <input type="submit" value="送信" class="btn btn-send">
            </form>
        </div>
        </section>
    
        <script src="js/vendor/jquery-2.2.2.min.js"></script>
        <script>
            $(function(){
                $("#js-scroll-bottom").animate({scrollTop: $("#js-scroll-bottom")[0].scrollHeight},"fast");
            });
        </script>
        </div>
        <?php
        require("footer.php");
        ?>