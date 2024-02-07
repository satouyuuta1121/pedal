<footer id="footer">
    copyright <a href="home.php">SHIMANOペダルレビュー</a>All Rights Reserved.
</footer>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<script>
    $(function(){
//フッター位置
    let $ftr = $('#footer');
    if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
      $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
    }
//メッセージ
    let $jsShowMsg=$("#js-show-msg");
    let msg=$jsShowMsg.text();
    if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
      $jsShowMsg.slideToggle("slow");
      setTimeout(function(){$jsShowMsg.slideToggle("slow");},5000);
    }
//ドラッグアンドドロップ
    let $dropArea=$(".area-drop");
    let $fileInput=$(".input-file");
    $dropArea.on("dragover",function(e){
      e.stopPropagation();
      e.preventDefault();
      $(this).css("border","3px #ccc dashed");
    });
    $dropArea.on("dragleave",function(e){
      e.stopPropagation();
      e.preventDefault();
      $(this).css("border","none");
    });
    $fileInput.on("change",function(e){
      $dropArea.css("border","none");
      let file=this.files[0],
      $img=$(this).siblings(".prev-img"),
      fileReader=new FileReader();

      fileReader.onload=function(event){
        $img.attr("src",event.target.result).show();
      };
      fileReader.readAsDataURL(file);
    });
//文字カウント
    let $countUp=$("#js-count"),
        $countView=$("#js-count-view");
      $countUp.on("keyup",function(e){
        $countView.html($(this).val().length);
      });
//画像スイッチ
      let $switchImgSubs=$(".js-switch-img-sub"),
          $switchImgMain=$("#js-switch-img-main");
        $switchImgSubs.on("click",function(e){
          $switchImgMain.attr("src",$(this).attr("src"));
        });
//お気に入り
        let $like
            likeProductId;

            $like=$(".js-click-like") || null;
            likeProductId= $like.data("productid") || null;

            if(likeProductId !== undefined && likeProductId !== null){
              $like.on("click",function(){
                let $this=$(this);
                $.ajax({
                  type: "POST",
                  url: "ajaxLike.php",
                  data: { productId : likeProductId}
                }).done(function(data){
                  console.log("ajax Success");
                  $this.toggleClass("active");
                }).fail(function(msg){
                  console.log("ajax Error");
                });
              });
            }

    });
</script>