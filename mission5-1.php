<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>

<body>

<h1>パスワード付き・編集機能付き・削除機能付き掲示板</h1>
<h2 style = "color: Green;">この掲示板の使い方</h2>
<h3 style = "color: DarkGreen;">・投稿を行う場合 </h3>
<h4>名前、コメント、パスワードを記入して送信してください。</h4>
<h3 style = "color: DarkGreen;">・削除を行う場合 </h3>
<h4>投稿番号と設定したパスワードを入力して送信してください</h4>
<h3 style = "color: DarkGreen;">・編集を行う場合 </h3>
<h4>編集したい記事の投稿番号と設定したパスワードを入力して送信し、<br>上のフォームで編集を行ってからパスワードとともに送信してください。<br>
※なお、再編集の際にもパスワード入力が必要です。</h4>
<!--　<質問>：①なぜ入力フォームの左上に空間があるのか
            　②全部「送信」のボタン表示のままで良いのか。
            　③SELECT文でデータ1つだけを取得してくる方法は無いのか。（現在はfetchAllでごり押し中）
            　④deleteのときに番号を1つずつ下げる方法
-->         

<?php

//ネットワーク接続
$dsn = 'mysql:dbname=データベース名;host=localhost';
$user = "ユーザー名"; 
$password = "パスワード";
//↓データべーす接続のための記述！！
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    $initial_name = "伊藤　太郎";
    $initial_comment = "○○○なう";
    $initial_newEditNumber = 0;
    $add_password = "";
    $delete_password = "";
    $edit_password = "";

    //テーブル「BulletinBoard」がなければ新規作成
    $sql = "CREATE TABLE IF NOT EXISTS BulletinBoard"
    ." ("
    //下の5つの項目を設定する。(ID, name, comment, date, password)
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name CHAR(32),"
    . "comment TEXT,"
    . "date TEXT,"
    . "password TEXT"
    .");";
    $stmt = $pdo->query($sql);
    
    //いったんパスワードやばい問題は保留
    if (isset($_POST['add_password']))  $add_password = $_POST["add_password"];
    
    //新奇送信がされた場合
    if (isset($_POST['add'])){
        
        $newEditNumber = 0;
        if (isset($_POST['newEditNumber'])){
            $newEditNumber = $_POST["newEditNumber"];
        }
        
        $comment = $_POST["comment"];
        $name = $_POST["Name"];
     //   if($name = "") $name = "名無しさん"; 
        $date = date("Y/m/d H:i:s");
        
        //編集をしたい！
        if(!empty($comment) && !empty($newEditNumber)){
            
            $sql = 'UPDATE BulletinBoard SET name=:name,comment=:comment,date=:date,password=:password WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':id', $newEditNumber, PDO::PARAM_INT);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':password', $add_password, PDO::PARAM_STR);
            $stmt->execute();
            
        //追加をしたい！！
        }else if(!empty($comment)){

            $sql = "INSERT INTO BulletinBoard (name, comment, date, password) 
                    VALUES (:name, :comment, :date, :password)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':password', $add_password, PDO::PARAM_STR);
            $stmt->execute();
            
        }
    }

    if(isset($_POST['delete_password'])) $delete_password = $_POST["delete_password"];
    if(isset($_POST['deleteNumber']) && !empty($_POST['deleteNumber'])){
        $deleteNumber = $_POST["deleteNumber"];
        //$oneLine = explode("<>",$file[$deleteNumber-1]);
        $sql = 'SELECT * FROM BulletinBoard WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $deleteNumber, PDO::PARAM_INT);
        $stmt->execute();
        //書き方下手すぎる
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            $password = $row['password'];
        }

        //削除がしたい！！
        if(isset($_POST['delete']) && ($password == $delete_password)){
            
            $sql = "SELECT COUNT(*) FROM BulletinBoard";
            $stmt = $pdo->query($sql);
            $count = $stmt->fetchColumn();
        
            $sql = 'delete from BulletinBoard where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $deleteNumber, PDO::PARAM_INT);
            $stmt->execute();
            
            /*
            for($i = $deleteNumber+1; $i <= $count; $i++){
                echo "hello\n";
                $sql = 'UPDATE BulletinBoard SET id=:id where id=:i';
                $stmt = $pdo->prepare($sql);
                $correctId = $i - 1; 
                $stmt->bindParam(':id', $correctId, PDO::PARAM_INT);
                $stmt->execute();
            }
            */
            
        }
    }
    if(isset($_POST['edit_password'])) $edit_password = $_POST["edit_password"];
    if(isset($_POST['editNumber']) && !empty($_POST['editNumber'])){
        $editNumber = $_POST["editNumber"];
        //$oneLine = explode("<>",$file[$editNumber-1]);
        $sql = 'SELECT password FROM BulletinBoard WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $editNumber, PDO::PARAM_INT);
        $stmt->execute();
        //書き方下手すぎる
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            $password = $row['password'];
        }

        //編集番号を表示したい！！
        if(isset($_POST['edit']) && ($password == $edit_password)){
            
            $sql = 'SELECT * FROM BulletinBoard WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $editNumber, PDO::PARAM_INT);
            $stmt->execute();
            
            $results = $stmt->fetchAll();
            
            foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る！！番号じゃなくて名称でいいのは便利！
                $initial_name = $row['name'];
                $initial_comment = $row['comment'];
                $initial_newEditNumber = $row['id'];
            }
        }
    }
    
    
?> 
        <form action = "" method = "post">
        　  <input type = "text" name = "Name"  value=<?= $initial_name ?>>
            <input type = "text" name = "comment"  value=<?= $initial_comment ?>>
            <input type = "text" name = "add_password"  placeholder ="パスワード">
            <input type = "submit" name= "add"> <br>
            <input type = "text" name = "deleteNumber"  placeholder="削除対象番号">
            <input type = "text" name = "delete_password"  placeholder ="パスワード">
            <input type = "submit" name = "delete"><br>
            <input type = "text" name = "editNumber"  placeholder="編集対象番号">
            <input type = "text" name = "edit_password"  placeholder ="パスワード">
            <input type = "submit" name = "edit"><br>
            <input type = "hidden" name = "newEditNumber"  value=<?= $initial_newEditNumber ?>>
        </form>
    
<?php

    $sql = 'SELECT * FROM BulletinBoard';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
 
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る！！番号じゃなくて名称でいいのは便利！
        echo $row['id'].' ';
        echo $row['name'].' ';
        echo $row['comment'].' ';
        echo $row['date'].' ';
        echo $row['password'].' ';
        echo "<br>";
    }
    
?>
    
</body>
</html>