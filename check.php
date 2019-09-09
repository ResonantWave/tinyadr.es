<?php
$DBH = new PDO("mysql:host=localhost;dbname=Tinyadres;charset=utf8", "root", "");

// end(explode('/', $_SERVER["REQUEST_URI"]));
$response = "<div>To check an URL, please use the format <a href='https://tinyadr.es/check/YsMRW'>tinyadr.es/check/YsMRW</a>, replacing the last part with your URL, or just use the following form:</div>";

if ($_SERVER['REQUEST_METHOD']=='POST') {
        if(!trim($_POST['shortURL']) == '' && !empty($_POST['shortURL'])) {
		if(strlen($_POST['shortURL']) == 5) {
			$url = $_POST['shortURL'];
		} else if (strpos($_POST['shortURL'], 'tinyadr') !== false) {
			$url = end(explode('/', $_POST['shortURL']));
		} else {
			$url = "false";
		}
		if ($url != "false") {
			$result = $DBH->prepare("SELECT * FROM URLs WHERE BINARY shortURL = :url");
        		$result->bindParam(":url", $url);
        		if ($result->execute()) {
                		if($result->rowCount() == 0) {
                        		$message = $response;
                		} else {
                        		while ($row = $result->fetch()) {
                                		$message = "<div>The URL <a rel='nofollow' href='https://tinyadr.es/" . $row['shortURL'] . "'>" . $row['shortURL'] . "</a> is redirecting to<br>" . $row['destURL'] . "</div>";
                        		}
                		}
        		}
		}
	}
} else {
	if(strlen(end(explode('/', $_SERVER["REQUEST_URI"]))) == 5) {
		$shortUrl = end(explode('/', $_SERVER["REQUEST_URI"]));
		$result = $DBH->prepare("SELECT * FROM URLs WHERE BINARY shortURL = :url");
		$result->bindParam(":url", $shortUrl);
		if ($result->execute()) {
			if($result->rowCount() == 0) {
				$message = $response;
			} else {
				while ($row = $result->fetch()) {
					$message = "<div>The URL <a rel='nofollow' href='https://tinyadr.es/" . $row['shortURL'] . "'>" . $row['shortURL'] . "</a> is redirecting to<br>" . $row['destURL'] . "</div>";
				}
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Tinyadr.es - For a smaller Internet!</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="/css/style.css" media="screen" />
	</head>
	<body>
		<div class="urlForm">
<?php if(!empty($message)) echo $message . "<br>"; ?>
			<form action="/check" method="post">
                        	<input name="shortURL" placeholder="URL to check" type="text">
                        	<input type="submit" value="Check URL">
                	</form>
		</div>
                <div id="bottomLeft">
                       	<a href="/tos">Terms of Service</a>
<?php /*<a href="/about">About</a>*/ ?>
                </div>
                <div id="bottomRight">
                       	<a href="/">Main page</a>
                </div>
	</body>
</html>
