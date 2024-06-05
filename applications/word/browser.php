<?php
	require_once(__DIR__ . '/class/Category.php');
	require_once(__DIR__ . '/class/Document.php');

    $catMan = new CategoryManager();
    $docMan = new DocumentManager();

    $categories = $catMan->getByServerKey($server->serverKey());

    if(count($categories) === 0){
        echo('Please configure the application.');
        die();
    }

    if(!isset($_GET['category']) || !isset($_GET['page'])){
        $cat_id = $categories[array_rand($categories)]->id();
        $page = 0;
    }else{
        $cat_id = $_GET['category'];
        $page = $_GET['page'];

        // check if the category is accessible
        $found = false;
        foreach($categories as $cat){
            if($cat->id() == $cat_id){
                $found = true;
                break;
            }
        }
        if(!$found)
            die('Invalid category');
        
        unset($found);
    }

    define("DOC_PER_PAGE", 13);
    define("PAGES_SHOWN", 2);

    parse_str($_SERVER['QUERY_STRING'], $url_query);

    $docCount = $docMan->countByCategory($server->serverKey(), $cat_id);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8"/>
    <script src="//feeps.ovh/js/jquery-3.5.1.min.js"></script>
    <script src="https://use.fontawesome.com/a243bf3a8c.js"></script>

	<script src="//feeps.ovh/applications/word/js/toastr.min.js"></script>
	<link rel="stylesheet" href="//feeps.ovh/applications/word/css/toastr.min.css">

	<script src="//feeps.ovh/js/language.min.js"></script>

    <link rel="stylesheet" href="//feeps.ovh/applications/word/css/browser.min.css">
</head>
<body>
	<!-- partial:index.partial.html -->
	<div class="wui-side-menu open pinned" data-wui-theme="dark">
		<div class="wui-side-menu-header">
			<a href="#" class="wui-side-menu-trigger"><i class="fa fa-bars"></i></a>
			<a href="#" class="wui-side-menu-pin-trigger">
				<i class="fa fa-thumb-tack"></i>
			</a>
		</div>
		<ul class="wui-side-menu-items">
			<?php
                foreach ($categories as $cat){
                    $cat_query = $url_query;

                    $cat_query['page'] = 0;
                    $cat_query['category'] = $cat->id();

                    echo('<li><a href="?' . http_build_query($cat_query) . '" class="wui-side-menu-item" id="">' . htmlspecialchars($cat->name()) . '</a></li>');
                }
			?>
		</ul>
	</div>
	<div class="wui-content">
		<div class="wui-content-header">
			<a href="#" class="wui-side-menu-trigger"><i class="fa fa-bars"></i></a>

			<span>
				<span class="title" cc-lang="document" id="lListDoc"></span>
				<span class="title" style="float: right">
                    <?= $docCount ?><span cc-lang="result"></span>
				</span>
			</span>

		</div>
		<div class="wui-content-main">
			<table>
				<thead>
					<tr>
						<th scope="col" cc-lang="title" id="lTitle"></th>
						<th scope="col" cc-lang="author" id="lAuthor"></th>
						<th scope="col" cc-lang="action" id="lAction"></th>
					</tr>
				</thead>
				<tbody>
					<?php
                        $documents = $docMan->getInRange($cat_id, $page * DOC_PER_PAGE, DOC_PER_PAGE);
                        foreach ($documents as $doc){
                            ?>

                            <tr docid="<?= $doc->id(); ?>">
                                <td><?= htmlspecialchars($doc->title()); ?></td>
                                <td class="tool-tip">
                                    <span cc-lang="name-<?= $doc->id(); ?>"><?= $doc->steamId(); ?></span>
                                    <script>
                                        gmod.requestName("name-<?= $doc->id(); ?>", "<?= $doc->steamId(); ?>");
                                    </script>
                                    <p class="tool-tip__info">SteamID64: <?= $doc->steamId(); ?></p>
                                </td>

                                <td>
                                    <?php
                                        if($doc->steamId() == $player_key->steamId() || $player_key->admin()){
                                    ?>

                                        <button class="actionBtn removeBtn"><i class="fa fa-trash"></i></button>
                                        <button class="actionBtn editBtn"><i class="fa fa-pencil"></i></button>
                                        
                                    <?php
                                        }
                                    ?>
                                    <button class="actionBtn viewBtn"><i class="fa fa-eye"></i></button>
                                </td>
                            </tr>

                            <?php
                        }
					?>
				</tbody>
			</table>



			<div style="text-align:center">
				<div class="pagination">
					<?php
                        $pagesCount = floor($docCount/DOC_PER_PAGE);

                        if ($page > 0) {
							// "back" buttons
							$backQuery = $url_query;
							$backQuery['page'] = $page - 1;
					
							$toVars = http_build_query($backQuery);
							echo "<a href='?$toVars'>&laquo;</a>";
						}
						
						foreach (range($page - PAGES_SHOWN, $page + PAGES_SHOWN) as $number) {
							if ($number < 0 || $number > $pagesCount) {
								continue;
							}

							$query = $url_query;
							$query['page'] = $number;
							$toVars = http_build_query($query);

							echo "<a " . ($page == $number ? "class='active'" : "") . " href='?$toVars'>$number</a>";
						}
						
						if ($page < $pagesCount) {
							// "next" button
							$backQuery = $url_query;
							$backQuery['page'] = $page + 1;
					
							$toVars = http_build_query($backQuery);
							echo "<a href='?$toVars'>&raquo;</a>";
						}
					?>
				</div>
			</div>
		</div>
	</div>
    <div class="wui-overlay"></div>


	<div id="removeModal" class="modal">
		<div class="modal-content" style="max-width:550px;">
			<div class="modal-header">
				<i class="fa fa-trash" cc-lang="remTitle"></i>
			</div>

			<div class="modal-body">
				<p cc-lang="remDesc"></p>
				<button id="remBtn" cc-lang="remBtn"></button>
			</div>
		</div>

	</div>

    <script>
        /*const gmod = {};
        gmod.editDoc = function(docId){
            console.log("Override me !");
        };

        gmod.viewDoc = function(docId){
            console.log("Override me !");
        };
        
        gmod.requestName = function(lId, steamId){
            console.log("Override me !");
        }
        
        */

        var sending = false;
        $(".removeBtn").each(function(){
            $(this).click(function(){
                if(sending){
                    return;
                }


                var parent = $(this).parent().parent();
                var docId = parent.attr("docid");
                if (docId){
                    $("#removeModal").show();

                    $("#remBtn").unbind("click");
                    $("#remBtn").click(function(){
                        $("#removeModal").hide();
                        sending = true;
                        $.post(window.location.href + "&service=remove", {
                            doc_id: docId
                        }).done(function(data){
                            if (data.status == 1){
                                parent.fadeOut(300, function(){
                                    parent.remove();
                                });
                            }else{
                                toastr.error(data.reason);
                            }
                            sending = false;
                        });
                    });
                }
            });
        });

        $(window).click(function(e) {
            if (e.target == $("#removeModal")[0]) {
                $("#removeModal").hide();
            }
        });

        $(".editBtn").each(function(){
            $(this).click(function(){
                var parent = $(this).parent().parent();
                var docId = parent.attr("docid");
                if (docId){
                    gmod.editDoc(docId);
                }
            });
        });

        $(".viewBtn").each(function(){
            $(this).click(function(){
                var parent = $(this).parent().parent();
                var docId = parent.attr("docid");
                if (docId){
                    gmod.viewDoc(docId);
                }
            });
        });
    </script>

    <script src="//feeps.ovh/applications/word/js/browser.min.js"></script>
</body>
</html>
