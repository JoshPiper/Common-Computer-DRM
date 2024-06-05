<?php
	require_once(__DIR__ . '/class/Category.php');
	require_once(__DIR__ . '/class/Background.php');

	$bgMan = new BackgroundManager();
	$catMan = new CategoryManager();

	$categories = $catMan->getByServerKey($server->serverKey());
	$backgrounds = $bgMan->getByServerKey($server->serverKey());

	if(count($categories) == 0 || count($backgrounds) == 0){
		die('Please configure the application first !');
	}

	if(isset($_GET['edit'])){
		require_once(__DIR__ . '/class/Document.php');
		$docMan = new DocumentManager();
		$doc = $docMan->getById($_GET['edit']);

		if(!$doc || ($doc->serverKey() != $server->serverKey() || (!$player_key->admin() && $doc->steamId() != $player_key->steamId()))){
			unset($doc);
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<script src="//feeps.ovh/js/jquery-3.5.1.min.js"></script>
	<script src="https://use.fontawesome.com/a243bf3a8c.js"></script>

	<script src="//feeps.ovh/js/language.min.js"></script>

	<script src="//feeps.ovh/js/combo-replacements.min.js"></script>
	<link rel="stylesheet" href="//feeps.ovh/css/combo-replacements.min.css">

	<script src="//feeps.ovh/js/jquery.modal.min.js"></script>
	<link rel="stylesheet" href="//feeps.ovh/css/jquery.modal.min.css">

	<script src="//feeps.ovh/applications/word/js/medium-editor.min.js"></script>
	<link rel="stylesheet" href="//feeps.ovh/applications/word/css/medium-editor.min.css">
	<link rel="stylesheet" href="//feeps.ovh/applications/word/css/themes/beagle.min.css">

	<script src="//feeps.ovh/applications/word/js/toastr.min.js"></script>
	<link rel="stylesheet" href="//feeps.ovh/applications/word/css/toastr.min.css">

	<link rel="stylesheet" href="//feeps.ovh/applications/word/css/editor.min.css">
	<link rel="stylesheet" href="//feeps.ovh/applications/word/css/template.min.css">
	
	<link rel="stylesheet" href="//feeps.ovh/css/owl.carousel.min.css">
	<link rel="stylesheet" href="//feeps.ovh/css/owl.theme.default.min.css">
	<script src="//feeps.ovh/js/owl.carousel.min.js"></script>
</head>
<body>
	<div class="editor">
		<div id="text-area"></div>
	</div>


	<div id="publishModal" class="modal">
		<h3 class="publishTitle" cc-lang="docPublish"></h3>
		<table class="catInput">
			<tr>
				<td><label cc-lang="title"></label></td>
				<td><input id="titleInput" type="text" class="catInput titleInput"><br></td>
			</tr>
			<tr>
				<td><label cc-lang="category"></label></td>
				<td>
					<div class="gmod-select catInput">
						<select id="catInput">
							<?php
								foreach($categories as $cat){
									?>
									<option value="<?= $cat->id() ?>"><?= htmlspecialchars($cat->name()) ?></option>
									<?php
								}
							?>
						</select>
					</div>
				</td>
			</tr>
		</table>

		<br>
		<button id="publishBtn" cc-lang="publish"></button>
		<br>
	</div>

	<div id="backModal" class="modal backModal">
		<div id="cac" class="owl-carousel owl-theme">
			<?php
				foreach($backgrounds as $bg){
					?>

					<div bgid="<?= $bg->id();?>" class="bgItem">
						<img class="bgImg" src="<?= htmlspecialchars($bg->url());?>">
						<p class="bgImgName"><?= htmlspecialchars($bg->name());?></p>
					</div>

					<?php
				}
			?>
		</div>
	</div>

	<script>
		const editor = {};
		/*const gmod = {};
		gmod.close = function(){
			console.log("Override me !");
		};*/

		var openBackModal = function openBackModal(){
			$("#backModal").modal({
				showClose: false
			});
		};

		var openPublishModal = function openPublishModal(){
			$("#publishModal").modal();
		};

		$(document).ready(function(){
			gmodCombo.init();

			const textarea = document.getElementById('text-area');

			
			editor.setBackground = function(url){
				$(textarea).parent().css("background", "url(" + url + ")");
			};

			editor.setPlaceholder = function(text){
				$(textarea).attr("data-placeholder", text);
			}

			$(textarea).on('keydown paste', function(e) {
				if (e.keyCode != 8 && this.offsetHeight > 800) {
					e.preventDefault();
				}
			});

			const me = new MediumEditor(textarea, {
				toolbar: {
					buttons: ["bold", "italic", "underline", "strikethrough", "h2", "justifyLeft", "justifyCenter", "justifyRight", "image"]
				},
				buttonLabels: 'fontawesome'
			});

			var owl = $('.owl-carousel');

			owl.owlCarousel({
				center: true,
				items: 2,
				nav: false,
				dots: false
			});

			var selBack = 0;
			<?php
				if(isset($doc)){
					require_once('vendor/HTMLPurifier/HTMLPurifier.auto.php');

					$docContent = strip_tags($doc->content(), '<h2><div><p><b><img><br><strike><i>');
                
					$config = HTMLPurifier_Config::createDefault();
					$purifier = new HTMLPurifier($config);
					$cleanHTML = $purifier->purify($docContent);
	
					echo('me.setContent("' . addslashes($cleanHTML) . '");' . PHP_EOL);
					echo('selBack = ' . $doc->background() . ';' . PHP_EOL);

					// Find the backgorund
					foreach($backgrounds as $bg){
						if($bg->id() == $doc->background()){
							echo('editor.setBackground("' . htmlspecialchars($bg->url()) . '");' . PHP_EOL);
							break;
						}
					}

					echo('$("#titleInput").val("' . addslashes(htmlspecialchars($doc->title())) . '");' . PHP_EOL);
					echo('$("#catInput").val(' . $doc->category() . ');' . PHP_EOL);
				}else{
					// Default config (No id)
					// Random background
					$r_back = $backgrounds[array_rand($backgrounds)];
					echo('selBack = ' . $r_back->id() . ';'  . PHP_EOL);
					echo('editor.setBackground("' . htmlspecialchars($r_back->url()) . '");' . PHP_EOL);
				}
			?>

			owl.on('changed.owl.carousel', function(event){
				var div = $($($(".owl-stage")[0]).children()[event.item.index]).first().children().first();
				var img = div.find("img").first().attr("src");

				if(div.attr("bgid")){
					selBack = div.attr("bgid");
				}
				editor.setBackground(img);
			});

			var sending = false;
			$("#publishBtn").click(function(){
				if (sending){
					return;
				}

				sending = true;
				$.post(window.location.href + "&service=publish", {
					<?php if(isset($doc)){ echo("id: " . $doc->id() . ","); } ?>
					title: $("#titleInput").val(),
					background: selBack,
					content: me.getContent(),
					category: $("#catInput").val()
				}).done(function(data){
					if(data.status == 1){
						gmod.close();
					}else{
						toastr.error(data.reason);
					}
					sending = false;
				});
			});

			$("#printBtn").click(function(){
				gmod.close();
				gmod.print(docId);
			});
		});
	</script>
</body>
</html>