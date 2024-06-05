<?php
    // Load the minimum possible here (This page need to be fast)
    require_once(__DIR__ . '/class/Document.php');
    require_once(__DIR__ . '/class/Background.php');
    require_once('vendor/HTMLPurifier/HTMLPurifier.auto.php');

    $docMan = new DocumentManager();
    $bgMan = new BackgroundManager();

    $doc_id = $_GET['view'];
    $doc = $docMan->getById($doc_id);
    if(!$doc || $doc->serverKey() != $server->serverKey()){
        die('Invalid document');
    }

    $background = $bgMan->getById($doc->background());
    if(!$background){
        die('Invalid background (Bad timing)');
    }
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="//feeps.ovh/applications/word/css/template.min.css">
</head>
<body>
    <div class="editor" style="background: url('<?= htmlspecialchars(addslashes($background->url())) ?>');">
		<div id="text-area">
            <?php
                $docContent = strip_tags($doc->content(), '<h2><div><p><b><img><br><strike><i>');
                
                $config = HTMLPurifier_Config::createDefault();
                $purifier = new HTMLPurifier($config);
                $cleanHTML = $purifier->purify($docContent);

                echo($cleanHTML);
            ?>
        </div>
	</div>
</body>
</html>