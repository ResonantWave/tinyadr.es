<?php
$DBH = new PDO("mysql:host=localhost;dbname=Tinyadres;charset=utf8", "root", "");

$number = $DBH->prepare("SELECT COUNT(*) FROM URLs");
$number->execute();
$row = $number->fetch();
$number = $row['COUNT(*)'];

$seed = str_split('abcdefghjkmnpqrstuvwxyz'
                 .'ABCDEFGHJKLMNPQRSTUVWXYZ'
                 .'23456789');
shuffle($seed);

if ($_SERVER['REQUEST_METHOD']=='POST') {
	if(!trim($_POST['originalURL']) == '' && !empty($_POST['originalURL']) && strpos($_POST['originalURL'], 'tinyadr') !== true) {
		$check = $DBH->prepare("SELECT * FROM URLs WHERE destURL = :url");
		$check->bindParam(':url', $_POST['originalURL']);
		$check->execute();
		if($check->rowCount() == 0) {
			$query = $DBH->prepare("INSERT INTO URLs(shortURL, destURL, createdDate, expirationDate, IP) VALUES (:shortURL, :destURL, :createdDate, :expDate, :ip)");
			$rand = '';
			foreach (array_rand($seed, 5) as $k) $rand .= $seed[$k];
			$query->bindParam(":shortURL", $rand);
			$query->bindParam(":destURL", $_POST['originalURL']);
			$time = time();
			$query->bindParam(":createdDate", $time);
/*TODO EXPIRY DATE*/    $time2 = time() * 2;
			$query->bindParam(":expDate", $time2);
			$query->bindParam(":ip", $_SERVER["REMOTE_ADDR"]);
			if ($query->execute()) {
				$message = "<div id='select'><a href='https://tinyadr.es/" . $rand . "'>tinyadr.es/" . $rand . "</a></div><script>SelectText('select');</script><br>Press Ctrl+C to copy the URL<br>";
				if($_POST['qrcode'] == "Yes") {
					$qr = "<img class='qrcode' src='https://chart.googleapis.com/chart?chs=300x300&amp;cht=qr&amp;chl=" . urlencode("https://tinyadr.es/" . $rand) . "&amp;choe=UTF-8' title='QR code' />";
				}
			} else {
				$errors = $query->errorInfo();
        			if (!empty($errors)) {
					if($errors[1] == "1062") {
						if(strpos($errors[2], 'shortURL') !== false) {
							$message = "Duplicate short url. Please try again!";
						}
					}
        			}
			}
		} else {
			while ($row = $check->fetch()) {
				$message = "<div id='select'><a href='https://tinyadr.es/" . $row['shortURL'] . "'>tinyadr.es/" . $row['shortURL'] . "</a></div><script>SelectText('select');</script><br>Press Ctrl+C to copy the URL<br>";
				if($_POST['qrcode'] == "Yes") {
                                        $qr = "<img class='qrcode' src='https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode("https://tinyadr.es/" . $row['shortURL']) . "&choe=UTF-8' title='QR code' />";
                                }
			}
		}
	} else {
		$message = "Invalid URL<br>";
	}
} else {
	if(strlen(preg_replace("/\//", "", $_SERVER["REQUEST_URI"], 1)) > 2) {
		if (strpos($_SERVER["REQUEST_URI"], '/index.php?url=') !== false) {
			if($_SERVER["REQUEST_URI"] == "/index.php?url=") {
				header("Location: /");
			} else {
				$shortUrl = str_replace("/index.php?url=", "", $_SERVER["REQUEST_URI"]);
			}
		} else {
			$shortUrl = (preg_replace('/\//', '', $_SERVER["REQUEST_URI"], 1));
		}
		$result = $DBH->prepare("SELECT destURL FROM URLs WHERE BINARY shortURL = :url and Enabled = 1");
		$result->bindParam(":url", $shortUrl);
		if ($result->execute()) {
			if($result->rowCount() == 0) {
/* TODO VISIT COUNTER*/		header("HTTP/1.1 404 Not Found");
				$message = "That URL does not exist or has been removed!";
			} else {
				while ($row = $result->fetch()) {
					$url = $row['destURL'];
					if (!preg_match("~^(?:f|ht)tps?://~i", $url)) { // if url doesn't start with http or ftp, append
       						$url = "http://" . $url;
					}
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: " . $url);
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
		<meta name="description" content="Shorten your URLs with just a click!">
		<link rel="stylesheet" type="text/css" href="/css/style.css" media="screen" />
		<script>
			function SelectText(element) {
				var doc = document, text = doc.getElementById(element), range, selection;
				if (doc.body.createTextRange) {
					range = document.body.createTextRange();
					range.moveToElementText(text);
					range.select();
				} else if (window.getSelection) {
					selection = window.getSelection();
					range = document.createRange();
					range.selectNodeContents(text);
					selection.removeAllRanges();
					selection.addRange(range);
				}
			}
		</script>
	</head>
	<body>
<?php if(!empty($qr)) echo $qr . "<br>"; ?>
		<form action="/" method="post" class="urlForm">
<?php if(!empty($message)) echo $message . "<br>"; ?>
			<input name="originalURL" placeholder="URL to tinify" type="text">
			<input type="submit" value="Tinify it!">
			<br><br>
			<input type="checkbox" name="qrcode" value="Yes"> Show QR code?
			<br><br><br>
			<div style="font-size: 150%;"><?php echo $number; ?> tinified URLs!</div>
		</form>
		<div id="bottomLeft">
			<a href="/tos">Terms of Service</a>
<?php /*<a href="/about">About</a>*/ ?>
		</div>
		<div id="bottomRight">
			<a href="/check">URL Checker</a>
		</div>
	</body>
</html>
