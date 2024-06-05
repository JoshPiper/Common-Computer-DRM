<?php
	require_once(__DIR__ . '/class/Category.php');
	require_once(__DIR__ . '/class/Background.php');

	$bgMan = new BackgroundManager();
	$catMan = new CategoryManager();

	if(isset($_GET['remCat'])){
		$cat_id = $_GET['remCat'];

		if($cat = $catMan->getById($cat_id)){
			if($cat->serverKey() == $server->serverKey()){
				$catMan->remove($cat_id);
				echo(1);
			}
		}
		die();
	}elseif(isset($_POST['catName'])){
		$name = $_POST['catName'];
		if(isset($_POST['catId'])){
			$cat_id = $_POST['catId'];
			if($cat = $catMan->getById($cat_id)){
				if($cat->serverKey() == $server->serverKey()){
					$cat->setName($name);
					$catMan->update($cat);
				}
			}
		}else{
			if($catMan->countByServerKey($server->serverKey()) >= 128){
				die();
			}

			$cat = new Category(null, $server->serverKey(), $name);
			$catMan->add($cat);
		}

		die();
	}elseif(isset($_GET['remBg'])){
		$bg_id = $_GET['remBg'];
		if($bg = $bgMan->getById($bg_id)){
			if($bg->serverKey() == $server->serverKey()){
				$bgMan->remove($bg_id);
				echo(1);
			}
		}
		die();
	}elseif(isset($_POST['bgName']) && isset($_POST['bgUrl'])){
		$name = $_POST['bgName'];
		$url = $_POST['bgUrl'];
		if(isset($_POST['bgId'])){
			$bg_id = $_POST['bgId'];
			if($bg = $bgMan->getById($bg_id)){
				if($bg->serverKey() == $server->serverKey()){
					$bg->setName($name);
					$bg->setUrl($url);
					$bgMan->update($bg);
				}
			}
		}else{
			if($bgMan->countByServerKey($server->serverKey()) >= 128){
				die();
			}

			$bg = new Background(null, $name, $server->serverKey(), $url);
			$bgMan->add($bg);
		}

		die();
	}

	$categories = $catMan->getByServerKey($server->serverKey());
	$backgrounds = $bgMan->getByServerKey($server->serverKey());
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">

	<script src="//feeps.ovh/js/language.min.js"></script>
	<link rel="stylesheet" href="//feeps.ovh/applications/word/css/config.min.css">
	<script src="//feeps.ovh/js/jquery-3.5.1.min.js"></script>

	<script src="//feeps.ovh/js/combo-replacements.min.js"></script>
	<link rel="stylesheet" href="//feeps.ovh/css/combo-replacements.min.css">
	
	<link rel="stylesheet" href="//feeps.ovh/css/owl.carousel.min.css">
	<link rel="stylesheet" href="//feeps.ovh/css/owl.theme.default.min.css">
	<script src="//feeps.ovh/js/owl.carousel.min.js"></script>
</head>
<body>
	<div class="content">
		<div class="header">
			<h2 class="title">
				<img src="//feeps.ovh/images/logo.png">
				Word Config
			</h2>
			<div class="gmod-select configSelect">
				<select id="switchCfg">
					<option value="cat">Categories</option>
					<option value="back" <?php if(isset($_GET['back'])){echo('selected');}?>>Backgrounds</option>
				</select>
			</div>
		</div>

		<div id="backCfg">
			<div id="cac" class="owl-carousel owl-theme">
				<?php
					foreach($backgrounds as $bg){
						?>

						<div bgid="<?= $bg->id();?>" class="bgItem">
							<img class="bgImg" src="<?= htmlspecialchars($bg->url());?>">
							<p class="bgImgName"><?= htmlspecialchars($bg->name());?></p>
							<div class="bgBtnCont">
								<button class="remBgBtn interactBtn"><i class="fas fa-trash-alt"></i></button>
								<button class="editBgBtn interactBtn"><i class="fas fa-edit"></i></button>
							</div>
						</div>

						<?php
					}
				?>

				<div class="bgItem" id="newBgBtn"><img src="http://feeps.ovh/applications/word/assets/add.png"></div>
			</div>


			<div id="bgModal" class="modal">
				<div class="modal-content">
					<span class="close">×</span>
					<h3 id="bgNewTitle">Create a new background</h3>
					<h3 id="bgEditTitle">Edit a background</h3>

					<label>Name: </label>
					<input type="text" maxlength="64" id="bgName"></input>
					<br><br>

					<label>URL of the image: </label>
					<input type="text" maxlength="2048" id="bgUrl"></input>
					<br><br>
					
					<button id="sendBg" style="float:right; padding: 10px">Send</button>
					<br><br>
				</div>
			</div>

		</div>

		<div id="catCfg">
			<button id="newCatBtn" style="margin-top: 4px; padding: 10px">Create a new category</button>
			<table id="catList">
				<tr>
					<th>Category</th>
					<th>Action</th> 
				</tr>

				<?php
					foreach($categories as $cat){
						?>

						<tr catid="<?= $cat->id()?>">
							<td><?= htmlspecialchars($cat->name())?></td>
							<td>
								<button class="remBtn interactBtn"><i class="fas fa-trash-alt"></i></button>
								<button class="editBtn interactBtn"><i class="fas fa-edit"></i></button>
							</td>
						</tr>

						<?php
					}
				?>
			</table>

			<div id="catModal" class="modal">
				<div class="modal-content">
					<span class="close">×</span>
					<h3 id="catNewTitle">Create a new category</h3>
					<h3 id="catEditTitle">Edit a category</h3>
					<label>Name: </label>
					<input type="text" maxlength="64" id="catName"></input>
					<br><br>
					<button id="sendCat" style="float:right; padding: 10px">Send</button>
					<br><br>
				</div>
			</div>

		</div>
	</div>

	<script>
		$(document).ready(function(){
			gmodCombo.init();

			/*
				SWITCH
			*/
			{
				var catDiv = $("#catCfg");
				var backDiv = $("#backCfg");

				var onChange = function(){
					if($("#switchCfg").val() == "cat"){
						catDiv.show();
						backDiv.hide();
					}else{
						backDiv.show();
						catDiv.hide();
					}
				};
				onChange();
				$("#switchCfg").change(onChange);
			}

			/*
				CODE FOR CATEGORIES
			*/
			{
				var editCatModel = $("#catModal");
				var openCatModal = function(id){
					editCatModel.show();
					if(id){
						$("#catName").val($("tr[catid='" + id + "']").children().first().html());
						$("#catEditTitle").show();
						$("#catNewTitle").hide();
					}else{
						$("#catName").val("");
						$("#catNewTitle").show();
						$("#catEditTitle").hide();
					}

					$("#sendCat").click(function(){
						$.post(window.location.href, {
							catName: $("#catName").val(),
							catId: id
						}).done(function(data){
							location.reload();
						});
					});
				};
				var closeCatModal = function(){
					editCatModel.hide();
				};

				editCatModel.find(".close").click(function(){
					closeCatModal();
				});

				$("#newCatBtn").click(function(){
					openCatModal();
				});

				$(window).click(function(e) {
					if (e.target == editCatModel[0]) {
						closeCatModal();
					}
				});

				$(".remBtn").each(function(){
					$(this).on('click', function(){
						var p = $(this).parent().parent();
						var catid = p.attr("catid");
						$.get(window.location.href + "&remCat=" + catid, function(data){
							if(data == 1){
								p.fadeOut(300, function(){
									p.remove();
								});
							}
						});
					});
				});

				$(".editBtn").each(function(){
					$(this).on('click', function(){
						var p = $(this).parent().parent();
						var catid = p.attr("catid");
						openCatModal(catid);
					});
				});
			}

			/*
				CODE FOR BACKGROUNDS
			*/
			{
				var owl = $('.owl-carousel');

				owl.owlCarousel({
					center: true,
					items: 6,
					nav: false,
					dots: false
				});

				$(document).on("click", ".owl-item", function() {
					owl.trigger("to.owl.carousel", [$(this).index(), 150] );
				});

				var editBgModal = $("#bgModal");
				var openBgModal = function(id){
					if(id){
						var p = $("div[bgid="+id+"]");

						$("#bgName").val(p.find("p").first().html());
						$("#bgUrl").val(p.find("img").first().attr("src"));
						$("#bgEditTitle").show();
						$("#bgNewTitle").hide();
					}else{
						$("#bgName").val("");
						$("#bgUrl").val("");
						$("#bgNewTitle").show();
						$("#bgEditTitle").hide();
					}

					$("#sendBg").click(function(){
						$.post(window.location.href, {
							bgName: $("#bgName").val(),
							bgUrl: $("#bgUrl").val(),
							bgId: id
						}).done(function(data){
							window.location.href = window.location.href + "&back";
						});
					});

					editBgModal.show();
				};
				var closeBgModal = function(){
					editBgModal.hide();
				};

				editBgModal.find(".close").click(function(){
					closeBgModal();
				});

				$("#newBgBtn").click(function(){
					openBgModal();
				});

				$(".remBgBtn").click(function(){
					var p = $(this).parent().parent();
					var bgid = p.attr("bgid");
					$.get(window.location.href + "&remBg=" + bgid, function(data){
						if(data == 1){
							p.fadeOut(300, function(){
								p.parent().remove();
							});
						}
					});
				});
				
				$(".editBgBtn").click(function(){
					var p = $(this).parent().parent();
					var bgid = p.attr("bgid");
					openBgModal(bgid);
				});

				$(window).click(function(e) {
					if (e.target == editBgModal[0]) {
						closeBgModal();
					}
				});
			}
		});
	</script>
</body>
</html>