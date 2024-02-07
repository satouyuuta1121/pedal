<?php
require("function.php");

debug("マイページ");
debugLogStart();

require("auth.php");

$u_id=$_SESSION["user_id"];
$productData=getMyProducts($u_id);
$likeData=getMyLike($u_id);

debug("取得した商品データ".print_r($productData,true));
debug("取得した掲示板データ".print_r($boardData,true));
debug("取得したお気に入りデータ",print_r($likeData,true));
debug("画面表示処理終了");
?>
<?php
$pageTitle="マイページ";
require("head.php");
?>

<body class="page-mypage page-2colum page-logined">
    <style>
        #main{
            border:none !important;
        }
    </style>
    <?php
    require("header.php");
    ?>

    <p id="js-show-msg" style="display:none;" class="msg-slide">
    <?php echo getSessionFlash("msg_success");?>
    </p>
    <div id="contents" class="site-width">
        <h1 class="page-title">MYPAGE</h1>
        <section id="main">
            <section class="list panel-list">
                <h2 class="title" style="margin-bottom:15px;">
                登録商品一覧
                </h2>
                <?php
                if(!empty($productData)):
                    foreach($productData as $key => $val):
                    ?>
                    <a href="registProduct.php<?php echo (!empty(appendGetParam()))?appendGetParam()."&p_id=".$val["id"]:"?p_id=".$val["id"];?>" class="panel">
                    <div class="panel-head">
                        <img src="<?php echo showImg($val["pic1"]);?>" alt="<?php echo $val["name"];?>">
                    </div>
                    <div class="panel-body">
                        <p class="panel-title"><?php echo $val["name"];?></p>
                    </div>
                </a>
                <?php
                endforeach;
            endif;
            ?>
            </section>
            <style>
                .list{
                    margin-bottom:30px;
                }
            </style>
            <section class="list list-table">
               
                
                        <?php
                        if(!empty($bordData)){
                            foreach($bordData as $key => $val){
                                if(!empty($val["msg"])){
                                    $msg=array_shift($val["msg"]);
                                    ?>
                                    <tr>
                                        <td><?php echo (date("Y.m.d H:i:s",strtotime($msg["send_date"])));?></td>
                                        <td>○○ ○○</td>
                                        <td><a href="msg.php?m_id=<?php echo $val["id"];?>"><?php echo mb_substr (($msg["msg"]),0,40) ;?>...</a></td>
                                    </tr>
                                    <?php
                                }else{
                                    ?>
                                    <tr>
                                        <td>--</td>
                                        <td>○○ ○○</td>
                                        <td><a href="msg.php?m_id=<?php echo $val["id"];?>">まだメッセージはありません</a></td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </section>

            <section class="list panel-list">
                <h2 class="title" style="margin-bottom:15px;">
                お気に入り一覧
                </h2>
                <?php
                if(!empty($likeData)):
                foreach($likeData as $key => $val):
                ?>
                <a href="productDetail.php<?php echo (!empty (appendGetParam()))?appendGetParam()."&p_id=".$val["id"]:"?p_id=".$val["id"];?>" class="panel">
                    <div class="panel-head">
                        <img src="<?php echo showImg($val["pic1"]);?>" alt="<?php echo ($val["name"]);?>">
                    </div>
                    <div class="panel-body">
                        <p class="panel-title"><?php echo $val["name"];?></p>
                    </div>
                </a>
                <?php
                endforeach;
            endif;
            ?>
            </section>
        </section>
        <?php
        require("sidebar.php");
        ?>
    </div>
    <?php
    require("footer.php");
    ?>

