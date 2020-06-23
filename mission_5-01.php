<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>掲示板</title>
</head>
<body>
    <?php
        //DBに接続
        $dsn = 'mysql:dbname=*****;host=*****';
    	$user = '*****';
    	$password = '*****';
    	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    	
    	//テーブルがない時作成
    	$sql = "CREATE TABLE IF NOT EXISTS mission5tb"
    	." ("
    	. "id INT AUTO_INCREMENT PRIMARY KEY,"
    	. "name char(32),"
    	. "comment TEXT,"
    	."date char(64),"
    	."password char(64)"
    	.");";
    	$stmt = $pdo->query($sql);

        
        //フォームの初期化
        $form_text = "コメント";
        $form_name = "名前";
        $flag = 0;
        $pass = "";
        
        //編集時のflagの変更
        if(isset($_POST["edit"]) && isset($_POST["pass"])){
            $id = $_POST["edit"];
            $sql = 'SELECT * FROM mission5tb WHERE id='.$id;
        	$stmt = $pdo->query($sql);
        	$results = $stmt->fetchAll();
        	if($results[0][4] == $_POST["pass"]){
        	   $flag = $id;
        	   $form_text = $results[0][2];
        	   $form_name = $results[0][1];
        	   $pass = $results[0][4];
        	}
        }
    ?>
    
    <h1>美味しいお店紹介掲示板</h1>
    <form action="" method="post">
        <input type="text" name="text" placeholder="コメントを入力してください" value="<?php echo $form_text; ?>">
        <input type="text" name="name" placeholder="名前を入力してください" value="<?php echo $form_name;?>">
        <input type="hidden" name="flag" value="<?php echo $flag ?>">
        <input type="text" name="pass" placeholder="パスワードを入力してください" value="<?php echo $pass; ?>">
        <input type="submit" name="submit">
    </form>
    <form action="" method="post">
        <input type="number" name="delete" placeholder="削除する番号を入力してください">
        <input type="text" name="pass" placeholder="パスワードを入力してください">
        <input type="submit" name="delete_submit" value="削除">
    </form>
    <form action="" method="post">
        <input type="number" name="edit" placeholder="編集する番号を入力してください">
        <input type="text" name="pass" placeholder="パスワードを入力してください">
        <input type="submit" name="edit_submit" value="編集">
    </form>
    
    <?php
        if(isset($_POST["delete"]) && isset($_POST["pass"])){//削除
            $id = $_POST["delete"];
            $sql = 'SELECT * FROM mission5tb WHERE id='.$id;
        	$stmt = $pdo->query($sql);
        	$results = $stmt->fetchAll();
        	if($results[0][4] == $_POST["pass"]){
        	    $sql = 'delete from mission5tb where id='.$id;
            	$stmt = $pdo->prepare($sql);
            	$stmt->execute();
            	echo "<strong>".$id."を削除しました</strong><br>";
        	}
        }
        if($_POST["flag"] == 0){//新規投稿
            if(isset($_POST["text"]) && isset($_POST["name"]) && strlen($_POST["pass"])){
                $sql = $pdo -> prepare("INSERT INTO mission5tb (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
            	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
            	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            	$sql -> bindParam(':date', $date, PDO::PARAM_STR);
            	$sql -> bindParam(':password', $password, PDO::PARAM_STR);
            	$name = $_POST["name"];
            	$comment = $_POST["text"];
            	$date = date("Y年m月d日 H時i分s秒");
            	$password = $_POST["pass"];
            	$sql -> execute();
            }
        }else{//編集
            $id = $_POST["flag"]; //変更する投稿番号
        	$name = $_POST["name"];
        	$comment = $_POST["text"];
        	$sql = 'UPDATE mission5tb SET name=:name,comment=:comment WHERE id=:id';
        	$stmt = $pdo->prepare($sql);
        	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
        	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
        	$stmt->execute();
        }
        
        //投稿の表示
        echo "<hr>";
        $sql = 'SELECT * FROM mission5tb';
    	$stmt = $pdo->query($sql);
    	$results = $stmt->fetchAll();
    	foreach ($results as $row){
    		echo $row['id'].',';
    		echo $row['name'].',';
    		echo $row['comment'].",";
    		echo $row["date"].'<br>';
    	echo "<hr>";
    	}
    	
        
    ?>
</body>