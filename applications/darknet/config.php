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
        <script src="//feeps.ovh/applications/darknet/js/popper.min.js"></script>
        <script src="//feeps.ovh/applications/darknet/js/boostrap.min.js"></script>
        <script src="//feeps.ovh/applications/darknet/js/darknet.min.js"></script>

        <script src="//feeps.ovh/js/language.min.js"></script>

        <script src="//feeps.ovh/applications/word/js/toastr.min.js"></script>
	    <link rel="stylesheet" href="//feeps.ovh/applications/word/css/toastr.min.css">

        <script src="//feeps.ovh/js/jquery.modal.min.js"></script>
	    <link rel="stylesheet" href="//feeps.ovh/css/jquery.modal.min.css">
    </head>
    <body>
        <div class="jumbotron text-center">
            <h1 id="title">Configuration</h1>
        </div>
        
        <div class="container">
            <div id="categoriesContainer">
                <h5>
                    <span cc-lang="categories"></span>
                    <a class="btn btn-secondary" style="float: right" cc-lang="add" id="newCatBtn"></a>
                </h5>
                
                <div class="card-columns" id="categoriesList">

                </div>
            </div>
            <div id="articlesContainer">
                <h5>
                <span cc-lang="articles"></span>
                    <a class="btn btn-secondary" style="float: right" cc-lang="add" id="newArticleBtn"></a>
                </h5>
                                
                <div id="articlesList">
                    
                </div>

                <button class="btn btn-secondary" cc-lang="back" id="backBtn"></button>
            </div>
        </div>

        <div id="loadingModal" class="darkModal modal">
            <div style="text-align: center !important;">
                <span cc-lang="loading"></span><br>
                <div class="loader"></div>
            </div>
        </div>

        <div id="categoryModal" class="darkModal modal">
            <h6 cc-lang="cat-edit"></h6>
            <br>

            <div>
                <label cc-lang="name"></label>
                <input type="text" id="nameInput" maxlength="64"/>
            </div>

            <div>
                <label cc-lang="desc"></label>
                <input type="text" id="descInput"  maxlength="512"/>
            </div>

            <div>
                <label cc-lang="imageUrl"></label>
                <input type="text" id="imageInput"  maxlength="2048" placeholder="500x400 px"/>
            </div>

            <div>
                <label cc-lang="footerLabel"></label>
                <input type="text" id="footerInput"  maxlength="64"/>
            </div>
            
            <br>

            <button class="btn btn-secondary" cc-lang="send" id="sendBtn" style="float: right;"></button>
        </div>

        <div id="articleModal" class="darkModal modal">
            <h6 cc-lang="art-edit"></h6>
            <br>

            <div>
                <label cc-lang="name"></label>
                <input type="text" id="artNameInput" maxlength="64"/>
            </div>

            <div>
                <label cc-lang="desc"></label>
                <input type="text" id="artDescInput"  maxlength="512"/>
            </div>

            <div>
                <label cc-lang="imageUrl"></label>
                <input type="text" id="artImageInput"  maxlength="2048" placeholder="500x400 px"/>
            </div>

            <div>
                <label cc-lang="gmodClass"></label>
                <input type="text" id="artClassInput"  maxlength="64"/>
            </div>

            <div>
                <label cc-lang="price"></label>
                <input type="number" id="artPriceInput" min="0"/>
            </div>
            
            <br>

            <button class="btn btn-secondary" id="artSendBtn" style="float: right;" cc-lang="send"></button>
        </div>

        <script>
            $(document).ready(function(){
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
                    view.AddCategory(categoriesList, cat.id, cat.name, cat.description, cat.imageUrl, cat.footer, cat.articles, true);
                });
            });
        </script>
    </body>
</html>