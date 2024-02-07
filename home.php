<?php
require("function.php");

debug("トップページ");
debugLogStart();

$currentPageNum=(!empty($_GET["p"]))?$_GET["p"]:1;
$category=(!empty($_GET["c_id"]))?$_GET["c_id"]:"";
$sort=(!empty($_GET["sort"]))?$_GET["sort"]:"";

if(!is_int($currentPageNum)){
    error_log("エラー発生:指定ページに不正な値が入りました");
    header("Location:index.php");
}

$listSpan=20;
$currentMinNum=(($currentPageNum-1)*$listSpan);
$dbProductData=getProductList($currentMinNum,$category,$sort);
$dbCategoryData=getCategory();
debug("現在のページ".$currentPageNum);
debug("画面表示処理終了");
?>
<?php
$pageTitle="home";
require("head.php");
?>

<body class="page-home page-2colum">
    <?php
    require("header.php");
    ?>
    <img src="img/ヘッド.png" class="head-img">
    <div id="contents" class="site-width home-contents">
        <section id="home-sidebar">
            <form name="" method="get">
                <h1 class="title">カテゴリー</h1>
                    <div class="selectbox">
                        <span class="icn_select"></span>
                        <select name="c_id" >
                            <option value="0" <?php if(getFormData("c_id",true)==0){echo "selected";}?>>選択してください</option>
                            <?php
                            foreach($dbCategoryData as $key => $val){
                                ?>
                                <option value="<?php echo $val["id"]?>"<?php if(getFormData("c_id",true)==$val["id"]){echo "selected";}?>>
                                <?php echo $val["name"];?>
                            </option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <h1 class="title">表示順</h1>
                    <div class="selectbox">
                        <span class="icn_select"></span>
                        <select name="sort" id="">
                            <option value="0"<?php if(getFormData("sort",true)==0){echo "selected";}?>>選択してください</option>
                            <option value="1"<?php if(getFormData("sort",true)==1){echo "selected";}?>>重量が軽い順</option>
                            <option value="2"<?php if(getFormData("sort",true)==2){echo "selected";}?>>重量が重い順</option>
                        </select>
                    </div>
                    <input type="submit" value="検索する">
            </form>
        </section>
        <section id="main home-main" >
            <div class="search-title">
                <div class="search-left">
                    <span class="total-num"><?php echo sanitize($dbProductData["total"]);?></span>件の商品が見つかりました
                </div>
                <div class="search-right">
                   
                </div>
            </div>
            <div class="panel-list">
                <?php
                    
                    foreach($dbProductData["data"]as $key => $val):
                       
                ?>
                <a href="productDetail.php<?php echo (!empty(appendGetParam()))?appendGetParam()."&p_id=".$val["id"]:"?p_id=".$val["id"];?>" class="panel">
                <div class="panel-head">
                    <img src="<?php echo sanitize($val["pic1"]);?>" alt="<?php echo sanitize($val["name"]);?>">
                </div>
                <div class="panel-body">
                    <p class="panel-title"><?php echo sanitize($val["name"]);?> <span class="weight"><?php echo sanitize(number_format($val["weight"]));?>g</span></p>
                </div>
            </a>
            <?php
            endforeach;
            ?>
            </div>
        </section>
        </div>
        <?php
        require("footer.php");
        ?>

