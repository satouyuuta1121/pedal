<?php
require("function.php");

debug("商品登録編集ページ");
debugLogStart();

require("auth.php");

$p_id=(!empty($_GET["p_id"]))? $_GET["p_id"]:"";
$dbFormData=(!empty($p_id))? getProduct($_SESSION["user_id"],$p_id):"";
$edit_flg=(empty($dbFormData))? false:true;
$dbCategoryData=getCategory();

debug("商品id".$p_id);
debug("フォーム用dbデータ".print_r($dbFormData,true));
debug("カテゴリデータ".print_r($dbCategoryData,true));

if(!empty($p_id) && empty($dbFormData)){
    debug("GETパラメータの商品IDが違います。マイページへ遷移します。");
    header("Location:mypage.php");
}

if(!empty($_POST)){
    debug("post送信あり");
    debug("post情報".print_r($_POST,true));
    debug("file情報".print_r($_FILES,true));

    $name=$_POST["name"];
    $category=$_POST["category_id"];
    $weight=(!empty($_POST["weight"]))? $_POST["weight"]:0;
    $comment=$_POST["comment"];
    $pic1=(!empty($_FILES["pic1"]["name"]))?uploadImg($_FILES["pic1"],"pic1"):"";
    $pic1=(empty($pic1) && !empty($dbFormData["pic1"]))?$dbFormData["pic1"]:$pic1;
    $pic2=(!empty($_FILES["pic2"]["name"]))?uploadImg($_FILES["pic2"],"pic2"):"";
    $pic2=(empty($pic2) && !empty($dbFormData["pic2"]))?$dbFormData["pic2"]:$pic2;
    $pic3=(!empty($_FILES["pic3"]["name"]))?uploadImg($_FILES["pic3"],"pic3"):"";
    $pic3=(empty($pic3) && !empty($dbFormData["pic3"]))?$dbFormData["pic3"]:$pic3;

    if(empty($dbFormData)){
        validRequired($name,"name");
        validMaxLen($name,"name");
        validSelect($category,"category_id");
        validMaxLen($comment,"comment",500);
        //validRequired($weight,"weight");
    }else{
        if($dbFormData["name"]!==$name){
            validRequired($name,"name");
            validMaxLen($name,"name");
        }
        if($dbFormData["category_id"]!==$category){
            validSelect($category,"category_id");
        }
        if($dbFormData["comment"]!==$comment){
            validMaxLen($comment,"comment",500);
        }
        if($dbFormData["weight"]!==$weight){
            validRequired($weight,"weight");
        }
    }
    if(empty($err_msg)){
        debug("バリデおk");

        try{
            $dbh=dbConnect();
            if($edit_flg){
                debug("db更新");
                $sql="UPDATE product SET category=:category,name=:name,comment=:comment,weight=:weight,pic1=:pic1,pic2=:pic2,pic3=:pic3 WHERE user_id=:u_id AND id=:p_id";
                $data=array(":category"=>$category,":name"=>$name,":comment"=>$comment,":weight"=>$weight,":pic1"=>$pic1,":pic2"=>$pic2,":pic3"=>$pic3,":u_id"=>$_SESSION["user_id"],":p_id"=>$p_id);
            }else{
                debug("db新規登録");
                $sql="INSERT INTO product (category,name,comment,weight,pic1,pic2,pic3,user_id,create_date)VALUES(:category,:name,:comment,:weight,:pic1,:pic2,:pic3,:u_id,:date)";
                $data=array(":category"=>$category,":name"=>$name,":comment"=>$comment,":weight"=>$weight,":pic1"=>$pic1,":pic2"=>$pic2,"pic3"=>$pic3,":u_id"=>$_SESSION["user_id"],":date"=>date("Y-m-d H:i:s"));
            }
            debug("sql".$sql);
            debug("流し込みデータ".print_r($data,true));

            $stmt=queryPost($dbh,$sql,$data);

            if($stmt){
                $_SESSION["msg_success"]=SUC04;
                debug("マイページに遷移");
                header("Location:mypage.php");
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
$pageTitle=(!$edit_flg)? "商品登録":";商品編集";
require("head.php");
?>

<body class="page-profEdit page-2colum page-logined">
    <?php
    require("header.php");
    ?>
    <div id="contents" class="site-width">
        <h1 class="page-title"><?php echo (!$edit_flg)? "商品を登録する":"商品を編集する";?></h1>
        <section id="main">
            <div class="form-container">
                <form action="" method="post" class="form" enctype="multipart/form-data" style="width:100%;box-sizing:border-box;">
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg["common"])) echo $err_msg["common"];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg["name"])) echo "err";?>">
                商品名<span class="label-require">必須</span>
                <input type="text" name="name" value="<?php echo getFormData("name");?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg["name"])) echo $err_msg["name"];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg["category_id"])) echo "err";?>">
                カテゴリ<span class="label-require">必須</span>
                <select name="category_id" id="">
                    <option value="0" <?php if(getFormData("category_id")==0){echo "selected";}?>>選択してください</option>
                    <?php
                    foreach($dbCategoryData as $key=>$val){
                    ?>
                    <option value="<?php echo $val["id"]?>"<?php if(getFormData("category_id")==$val["id"]){echo "selected";}?>><?php echo $val["name"];?>
                    </option>
                    <?php
                    }
                    ?>
                </select>
                </label>
                <div class="area-msg">
                    <?php 
                    if(!empty($err_msg["category_id"])) echo $err_msg["category_id"];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg["comment"])) echo "err";?>">
                詳細
                <textarea name="comment" id="js-count" cols="30" rows="10" style="height:150px;"><?php echo getFormData("comment");?></textarea>
                </label>
                <p class="counter-text"><span id="js-count-view">0</span>/500文字</p>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg["comment"])) echo $err_msg["comment"];
                    ?>
                </div>
                <label style="text-align:left;" class="<?php if(!empty($err_msg["weight"])) echo "err";?>">
                重量<span class="label-require">必須</span>
                <div class="form-group">
                    <input type="text" name="weight" style="width:150px;" placeholder="0" value="<?php echo (!empty(getFormData("weight")))?getFormData("weight"):0;?>"><span class="option">円</span>
                </div>
            </label>
            <div class="area-msg">
                <?php
                if(!empty($err_msg["weight"])) echo $err_msg["weight"];
                ?>
            </div>
            <div style=overflow:hidden;">
                <div class="imgDrop-container">
                    画像1
                    <label class="area-drop<?php if(!empty($err_msg["pic1"])) echo "err";?>">
                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                    <input type="file" name="pic1" class="input-file">
                    <img src="<?php echo getFormData("pic1");?>" alt="" class="prev-img" style="<?php if(empty(getFormData("pic1"))) echo "display:none";?>">
                    ドラッグ＆ドロップ
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg["pic1"])) echo $err_msg["pic1"];
                    ?>
                </div>
                </div>
                
                <div class="imgDrop-container">
                    画像2
                    <label class="area-drop<?php if(!empty($err_msg["pic2"])) echo "err";?>">
                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                    <input type="file" name="pic2" class="input-file">
                    <img src="<?php echo getFormData("pic2");?>" alt="" class="prev-img" style="<?php if(empty(getFormData("pic2"))) echo "display:none";?>">
                    ドラッグ＆ドロップ
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg["pic2"])) echo $err_msg["pic2"];
                    ?>
                </div>
                </div>

                <div class="imgDrop-container">
                    画像3
                    <label class="area-drop<?php if(!empty($err_msg["pic3"])) echo "err";?>">
                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                    <input type="file" name="pic3" class="input-file">
                    <img src="<?php echo getFormData("pic3");?>" alt="" class="prev-img" style="<?php if(empty(getFormData("pic3"))) echo "display:none";?>">
                    ドラッグ＆ドロップ
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg["pic3"])) echo $err_msg["pic3"];
                    ?>
                </div>
                </div>
            </div>
            <div class="btn-container">
                <input type="submit" class="btn btn-mid" value="<?php echo ($edit_flg)? "登録する":"更新する";?>">
            </div>
                </form>
            </div>
        </section>
    <?php
    require("sidebar.php");
    ?>
    <?php
    require("footer.php");
?>
    
