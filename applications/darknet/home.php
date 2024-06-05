<?php
    require_once('class/Category.php');
    require_once('class/Article.php');

    $catMan = new CategoryManager();
    $artMan = new ArticleManager();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
        <link href="//feeps.ovh/applications/darknet/css/bootstrap-dark.min.css" rel="stylesheet" type="text/css">
        <link href="//feeps.ovh/applications/darknet/css/style.min.css" rel="stylesheet" type="text/css">
        <script src="//feeps.ovh/js/jquery-3.5.1.min.js"></script>
        <script src="//feeps.ovh/applications/darknet/js/darknet.min.js"></script>

        <script src="//feeps.ovh/js/language.min.js"></script>

        <script src="//feeps.ovh/js/combo-replacements.min.js"></script>
	    <link rel="stylesheet" href="//feeps.ovh/css/combo-replacements.min.css">

        <script src="//feeps.ovh/applications/word/js/toastr.min.js"></script>
	    <link rel="stylesheet" href="//feeps.ovh/applications/word/css/toastr.min.css">

        <script src="//feeps.ovh/js/jquery.modal.min.js"></script>
	    <link rel="stylesheet" href="//feeps.ovh/css/jquery.modal.min.css">
    </head>
    <body>
        <div class="jumbotron text-center">
            <h1 cc-lang="title"></h1>
            <p cc-lang="subtitle"></p>
        </div>
        
        <div class="container">
            <div id="categoriesContainer">
                <h5 cc-lang="categories"></h5>
                <div class="card-columns" id="categoriesList"></div>
            </div>
            <div id="articlesContainer">
                <h5 cc-lang="articles"></h5>
                <div id="articlesList"></div>
                <button class="btn btn-secondary" cc-lang="back" id="backBtn"></button>
            </div>
        </div>

        <div id="buyModal" class="darkModal modal">
            <h6><span cc-lang="goingBuy"></span><span id="itemName"></span></h6>
            <p>
            <span><strong cc-lang="price"></strong>: </span><span id="itemPrice"></span>$<br>
            <span><strong cc-lang="locChoose"></strong> </span>
            
            </p>

            <div class="gmod-select locSel">
                <select id="locationSelect"> <!-- Fill with Gmod -->
                </select>
            </div>

            <p cc-lang="modalFooter"></p>

            <br>
            <button class="btn btn-secondary" cc-lang="buy" id="buyBtn" style="float: right;"></button>
        </div>
        
        <div class="footer"><p cc-lang="footer"></p></div>

        <script>
            $(document).ready(function(){
                //gmodCombo.init();

                var categoriesList = document.getElementById("categoriesList");
                <?php
                    // Add categories from the database
                    $categoriesArray = array();
                    foreach($catMan->getByServerKey($server->serverKey()) as $category){
                        $cat = array();
                        $cat['id'] = $category->id();
                        $cat['name'] = $category->name();
                        $cat['description'] = $category->description();
                        $cat['imageUrl'] = $category->imageUrl();
                        $cat['footer'] = $category->footer();

                        // Add his articles
                        foreach($artMan->getByCategory($category->id()) as $article){
                            $art = array();
                            $art['id'] = $article->id();
                            $art['name'] = $article->name();
                            $art['imageUrl'] = $article->imageUrl();
                            $art['price'] = $article->price();
                            $art['class'] = $article->class();
                            $art['description'] = $article->description();

                            $cat['articles'][] = $art;
                        }

                        $categoriesArray[] = $cat;
                    }

                    echo('var categories = ' . json_encode($categoriesArray) . ';');
                ?>

                categories.forEach(function(cat){
                    view.AddCategory(categoriesList, cat.id, cat.name, cat.description, cat.imageUrl, cat.footer, cat.articles);
                });
            });
        </script>
    </body>
</html>