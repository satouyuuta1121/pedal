<?php
require("function.php");

debug("商品詳細");
debugLogStart();

$p_id=(!empty($_GET["p_id"]))? $_GET["p_id"]:"";
$viewData=getProductOne($p_id);


if(empty($viewData)){
    error_log("エラー発生:指定ページに不正な値が入りました");
    header("Location:home.php");
}
debug("取得したdbデータ".print_r($viewData,true));

if(!empty($_POST["submit"])){
    debug("post通信あり");

    require("auth.php");
    
      //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = "SELECT * product";
    $data = array(':s_uid' => $viewData['user_id'], ':b_uid' => $_SESSION['user_id'], ':p_id' => $p_id, ':date' => date('Y-m-d H:i:s'));
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    // クエリ成功の場合
    if($stmt){
      $_SESSION['msg_success'] = SUC05;
      debug('連絡掲示板へ遷移します。');
      header("Location:msg.php?m_id=".$dbh->lastInsertID()); //連絡掲示板へ
    }

  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
    debug("画面表示処理終了");

?>

<?php
$pageTitle="商品詳細";
require("head.php");
?>

<body class="page-productDetail page-1colum">
    <?php
    require("header.php");
    ?>
    <div id="contents" class="site-width">
        <div class="title">
            <h2><?php echo sanitize($viewData["name"]);?></h2>
            
        </div>
        <div class="product-img-container">
            <div class="img-main">
                <img src="<?php echo showImg(sanitize($viewData["pic1"]));?>" alt="メイン画像:<?php echo sanitize($viewData["name"]);?>" id="js-switch-img-main" class="img-main">
            </div>
            <div class="img-sub">
                <img src="<?php echo showImg(sanitize($viewData["pic1"])); ?>" alt="画像1:<?php echo sanitize($viewData["name"]);?>"class="js-switch-img-sub img-sub">
                <img src="<?php echo showImg(sanitize($viewData["pic2"])); ?>" alt="画像2:<?php echo sanitize($viewData["name"]);?>"class="js-switch-img-sub img-sub">
                <img src="<?php echo showImg(sanitize($viewData["pic3"])); ?>" alt="画像3:<?php echo sanitize($viewData["name"]);?>"class="js-switch-img-sub img-sub">
            </div>
        </div>
        <div class="product-detail">
            <p><?php echo sanitize($viewData["comment"]);?></p>
        </div>
        <div class="item-mid">
                <p class="weight">重量　両側<?php echo sanitize(number_format($viewData["weight"]));?>g</p>
            </div>
            
        <div class="product-buy">
            <div class="item-left">
                <a href="home.php<?php echo appendGetParam(array("p_id"));?>">&lt; 商品一覧に戻る</a>
            </div>
            </form>
            </section>
        </div>
        <div class="comment"></div>
            <?php
                 if(!empty($_SESSION["user_id"])){
            ?>
            <p>コメントはこちら</p>
            <form >
                <input type="text" placeholder="コメントはこちら">
            </form>
            <?php
                }else{
            ?>
            <p>コメントするには<a href="signup.php">登録する</a>か<a href="login.php">ログイン</a>してください</p>
            <?php
                }
            ?>
            <div>
                <form action="" method="post">
                    <div class="">
                        <input type="submit" value="コメントを投稿する" name="submit" class="btn btn-primary" style="margin-top:0;">
                    </div>
            </div>
        </div>

    <?php
    require("footer.php");
    ?>
    