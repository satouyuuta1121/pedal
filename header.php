
<header>
    <div class="site-width">
        <nav class=top-nav>
            <h1><a href="home.php"> SHIMANOペダルレビュー</a></h1>
            
            <ul>
                <?php
                if(empty($_SESSION["user_id"])){
                ?>
                <li><a href="signup.php" class="btn btn-primary">ユーザー登録</a></li>
                <li><a href="login.php" class="login">ログイン</a></li>
                <?php
                }else{
                ?>
                <li><a href="mypage.php">マイページ</a></li>
                <li><a href="logout.php">ログアウト</a></li>
                <?php
                }
                ?>
                
            </ul>
        </nav>
    </div>
</header>
