<!DOCTYPE html>
<html lang="ja">
<meta charset="utf-8">
<title>投稿サイト</title>
<link rel="stylesheet" href="css/styles.css">
<body>

<?php

$dsn = 'データベース名';	//データベースへの接続;
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

$sql = "CREATE TABLE IF NOT EXISTS toukou"	//テーブルの作成；
	." ("					
	. "id INT AUTO_INCREMENT PRIMARY KEY,"	
	. "name char(32),"
	. "comment TEXT,"
	. "jdate TEXT,"
	. "password TEXT"
	.");";
	$stmt = $pdo->query($sql);	//PDOStatement オブジェクトとして返す


//$sql = 'DROP TABLE toukou';	//テーブルの削除；
//	$stmt = $pdo->query($sql);


//$sql ='SHOW TABLES';	//テーブル一覧を表示；
	//$result = $pdo -> query($sql);
	//foreach ($result as $row){
		//echo $row[0];
		//echo '<br>';
	//}
	//echo "<hr>";


//$sql ='SHOW CREATE TABLE toukou';	//テーブルの中身を確認；
//	$result = $pdo -> query($sql);
//	foreach ($result as $row){
//		echo $row[1];
//	}
//	echo "<hr>";

////////////////名前・コメント書きこみフォーム////////////////////////////////////////////////////////////////////////////////


if(!empty($_POST["name"]) && !empty($_POST["comment"]) &&  !empty($_POST["password"]) && !empty($_POST["send"]) && empty($_POST["editNumber"]))		//名前・コメントが空ではないことを確認；
{
	$sql = $pdo -> prepare("INSERT INTO toukou (name, comment,  jdate, password) VALUES (:name, :comment, :jdate, :password )");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);	
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':jdate', $jdate, PDO::PARAM_STR);
	$sql -> bindParam(':password', $password, PDO::PARAM_STR);
	$name = $_POST["name"];
	$comment = $_POST["comment"]; 
	$date = new DateTime("now") ;
	$jdate = $date -> format('Y年n月j日 G時i分');
	//var_dump($jdate);
	$password = $_POST["password"];
	$sql -> execute();	//execute() 実行；
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



////////////////////削除処理フォーム////////////////////////////////////////////////////////////////////////////////

if(!empty($_POST["delete"]) && !empty($_POST["delPassword"]) && !empty($_POST["delButton"]))
{
	$id = $_POST["delete"];
	$delpass = $_POST["delPassword"];
	$sql = 'SELECT * FROM toukou';	//データを取得する；
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();	//配列
	//var_dump($results);
	foreach ($results as $row){	//$rowの中にはテーブルのカラム名が入る;
		//var_dump($row);
		if($id  == $row['id']){
			if($delpass == $row['password']){
				$sql = 'delete from toukou where id=:id';
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $id, PDO::PARAM_INT);
				$stmt->execute();
			}
			else{
				echo "パスワードが違います";
			}
		}
	}
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	

/////////////////////編集対象投稿の編集フォーム//////////////////////////////////////////////////////////////////////////////////////////////////////////////

if(!empty($_POST["editId"]) && !empty($_POST["editPassword"]) && !empty($_POST["editButton"])){
	$editpas = $_POST["editPassword"];
	$editiId = $_POST["editId"];	
	$editPassword = $_POST["editPassword"];
	$sql = 'SELECT * FROM toukou';	//データを取得する；
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();	//配列
	//var_dump($results);
	foreach ($results as $row){	//$rowの中にはテーブルのカラム名が入る;
		//var_dump($row);
		if($editiId  == $row['id']){
			if($editpas == $row['password']){
				$editNumber = $row['id'];	//投稿番号の取得;
				$editName = $row['name'];	//名前の取得；
				$editComment = $row['comment'];	//コメントの取得；
			}
			else{
				echo "パスワードが違います";
			}
		}
	}
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////編集した投稿の書き換えフォーム///////////////////////////////////////////////////////////////////////////////////////////

if(!empty($_POST["name"]) && !empty($_POST["comment"]) &&  !empty($_POST["password"]) && !empty($_POST["editNumber"]))
{
	$id = $_POST["editNumber"];
	$name = $_POST["name"];
	$comment =  $_POST["comment"];
	$date = new DateTime("now");
	$jdate = $date -> format('Y年n月j日 G時i分');
	$password = $_POST["password"];
	$sql = 'update toukou set name=:name,comment=:comment, jdate=:jdate, password=:password where id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	$stmt->bindParam(':jdate', $jdate, PDO::PARAM_STR);
	$stmt -> bindParam(':password', $password, PDO::PARAM_STR);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>



<form method="POST" action="">
	名前<br>
	<input type = "text"  name = "name" value = <?php
						if(!empty($editName))	//$editNameが空ではなかったとき；
						{	 
							echo $editName; 	//$editNameを出力;
						}
						?>><br/>

	コメント<br/>
	<input type = "text" name = "comment"  value = <?php 
						if(!empty($editComment))	//$editCommentが空ではなかったとき；
						{
							echo $editComment;	//$editeCommentを出力;
						}
						?>><br/>
	<input type = "hidden" name = "editNumber" value = <?php 
							if(!empty($editNumber))	//$editNumderが空ではなかったとき；
							{
								echo$editNumber;	//$editeNumberを出力;
							}
							?>>
	パスワード<br/>
	<input type = "password" name = "password">

	<input type = "submit" name = "send" value = "送信"><br/><br/>

	削除対象番号(半角)<br/>
	<input type = "text" name = "delete" ><br/>
	パスワード<br/>
	<input type = "password" name = "delPassword">
	<input type = "submit" name = "delButton" value = "削除"><br/><br/>

	編集対象番号(半角)<br/>
	<input type = "text" name = "editId"><br/>
	パスワード<br/>
	<input type = "password" name = "editPassword">
	<input type = "submit" name = "editButton" value = "編集"><br/><br/>
</form>


//////////////////////////////////////////////////////////////////////////////////////////////////

<?php

$sql = 'SELECT * FROM toukou';	//データを取得する；
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();	//配列
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['jdate'].',';
		echo $row['password'].'<br>';
	echo "<hr>";
	}
?>	


</body>

</html>