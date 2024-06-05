var cur_cat;

function showLoading(){
    $("#loadingModal").modal({
        escapeClose: false,
        clickClose: false,
        showClose: false
    });
}
const view = {};

function openCategoryModal(parent, id, name, description, image, footer){
    $("#sendBtn").unbind("click");

    if(id){
        $("#nameInput").val(name);
        $("#imageInput").val(image);
        $("#descInput").val(description);
        $("#footerInput").val(footer);

        var sending = false; // Just to avoid sending multiple time the same category
        $("#sendBtn").click(function(){
            if (sending){
                return;
            }
            
            showLoading();
            sending = true;
            $.post(window.location.href + "&service=edit_category", {
                id: id,
                name: $("#nameInput").val(),
                imageUrl: $("#imageInput").val(),
                description: $("#descInput").val(),
                footer: $("#footerInput").val()
            }).done(function(data){
                if(data.status == 1){
                    var data = data.value;
                    $(parent).find('h4').first()[0].textContent = data.name;
                    $(parent).find('p').first()[0].innerHTML = data.description;

                    var img = $(parent).find('img').first();

                    if((data.imageUrl && data.imageUrl.trim().lenght !== 0)){
                        img.attr("src", data.imageUrl);
                        img.show();
                    }else{
                        img.attr("src", "");
                        img.hide();
                    }
                    
                    $(parent).find('small').first()[0].textContent = data.footer;
                }else{
                    toastr.error(data.reason);
                }
                
                sending = false;
                $.modal.close();
            });
        });
    }else{
        $("#nameInput").val('');
        $("#imageInput").val('');
        $("#descInput").val('');
        $("#footerInput").val('');

        var sending = false; // Just to avoid sending multiple time the same category
        $("#sendBtn").click(function(){
            if (sending){
                return;
            }
            
            showLoading();
            sending = true;
            
            $.post(window.location.href + "&service=new_category", {
                name: $("#nameInput").val(),
                imageUrl: $("#imageInput").val(),
                description: $("#descInput").val(),
                footer: $("#footerInput").val()
            }).done(function(data){
                if(data.status == 1){
                    var data = data.value;
                    view.AddCategory(document.getElementById("categoriesList"), data.id, data.name, data.description, data.imageUrl, data.footer, {}, true);
                }else{
                    toastr.error(data.reason);
                }
                
                sending = false;
                $.modal.close();
            });
        });
    }
    
    $("#categoryModal").modal();
}

function openArticleModal(parent, id, name, description, imageUrl, gmodClass, price){
    $("#artSendBtn").unbind("click");
    if(id){
        $("#artNameInput").val(name);
        $("#artDescInput").val(description);
        $("#artImageInput").val(imageUrl);
        $("#artClassInput").val(gmodClass);
        $("#artPriceInput").val(price);

        $("#artSendBtn").click(function(){
            if (sending){
                return;
            }
            
            showLoading();
            sending = true;

            $.post(window.location.href + "&service=edit_article", {
                id: id,
                name: $("#artNameInput").val(),
                imageUrl: $("#artImageInput").val(),
                description: $("#artDescInput").val(),
                class: $("#artClassInput").val(),
                price: $("#artPriceInput").val()
            }).done(function(data){
                if(data.status == 1){
                    var data = data.value;
                    $(parent).find('h4').first()[0].textContent = data.name;
                    $(parent).find('span').first()[0].textContent = data.price;
                    $(parent).find('p').first().next()[0].innerHTML = data.description;
                    $(parent).find('input').first().val(data.class);
    
                    var img = $(parent).find('img').first();
                    if((data.imageUrl && data.imageUrl.trim().lenght !== 0)){
                        img.attr("src", data.imageUrl);
                        img.show();
                    }else{
                        img.attr("src", "");
                        img.hide();
                    }
                }else{
                    toastr.error(data.reason);
                }
                
                sending = false;
                $.modal.close();
            });
        });
    }else{
        $("#artNameInput").val('');
        $("#artDescInput").val('');
        $("#artImageInput").val('');
        $("#artClassInput").val('');
        $("#artPriceInput").val('');

        var sending = false; // Just to avoid sending multiple time the same category
        $("#artSendBtn").click(function(){
            if (sending){
                return;
            }
            
            showLoading();
            sending = true;
            
            $.post(window.location.href + "&service=new_article", {
                name: $("#artNameInput").val(),
                imageUrl: $("#artImageInput").val(),
                description: $("#artDescInput").val(),
                class: $("#artClassInput").val(),
                category: cur_cat,
                price: $("#artPriceInput").val()
            }).done(function(data){
                if(data.status == 1){
                    var data = data.value;
                    view.AddArticle(document.getElementById("articlesList"), data.id, data.name, data.description, data.price, data.imageUrl, data.class, true);
                }else{
                    toastr.error(data.reason);
                }
                
                sending = false;
                $.modal.close();
            });
        });

    }

    $("#articleModal").modal();
}

function openBuyModal(id, name, price){
    document.getElementById("itemPrice").textContent = price;
    document.getElementById("itemName").textContent = name;

    $("#buyBtn").unbind("click");
    $("#buyBtn").click(function(){
        gmod.buy(id, $("#locationSelect").val(), name, price);
        $.modal.close();
    });

    $("#buyModal").modal();
}

view.Notif = function(text, error){
    if(error){
        toastr.error(text);
    }else{
        toastr.success(text);
    }
};

view.PopulateCategory = function(name, articles, editBtn){
    var parent = document.getElementById("articlesList");
    $(parent).empty();
    articles.forEach(function(art){
        view.AddArticle(parent, art.id, art.name, art.description, art.price, art.imageUrl, art.class, editBtn);
    });
};

view.AddCategory = function(parent, id, name, description, image, footer, articles, editBtn){
    var div = document.createElement("div");
    div.className = "card";

    var card = document.createElement("div");
    card.className = "card-block";
    div.appendChild(card);

    var title = document.createElement("h4");
    title.className = "card-title";
    title.textContent = name;
    card.appendChild(title);

    var desc = document.createElement("p");
    desc.className = "card-text";
    desc.innerHTML = description;
    card.appendChild(desc);

    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "btn btn-secondary";
    btn.onclick = function(){
        $("#categoriesContainer").hide();
        $("#articlesContainer").show();

        cur_cat = id;
        view.PopulateCategory(name, articles, editBtn);
    };
    btn.setAttribute("cc-lang", "view");
    card.appendChild(btn);

    //var img;
   // if(image && image.trim().lenght !== 0){
        var img = document.createElement("img");
        img.className = "card-img-bottom";

        if((image && image.trim().lenght !== 0)){
            img.src = image;
            $(img).show();
        }else{
            img.src = "";
            $(img).hide();
        }

        img.style.margin = "10px 0px";
        div.appendChild(img);
   // }

    var footerDom = document.createElement("div");
    footerDom.className = "card-footer";
    div.appendChild(footerDom);

    var footerText = document.createElement("small");
    footerText.className = "text-muted";
    footerText.textContent = footer;
    footerDom.appendChild(footerText);

    if (editBtn){
        var editContainer = document.createElement("div");
        editContainer.classList.add("editContainer");
        div.appendChild(editContainer);

        var remBtn = document.createElement("button");
        remBtn.innerHTML = '<i class="fas fa-trash-alt"></i>';
        remBtn.classList.add("interactBtn");
        var sending = false;
        remBtn.onclick = function(){
            if (sending){
                return;
            }

            showLoading();
            sending = true;

            $.post(window.location.href + "&service=rem_category", {
                id: id,
            }).done(function(data){
                if(data.status == 1){
                    $(div).fadeOut(200, function(){
                        $(this).remove();
                    });
                }else{
                    toastr.error(data.reason);
                }
                
                sending = false;
                $.modal.close();
            });
        };
        editContainer.appendChild(remBtn);

        var editBtn = document.createElement("button");
        editBtn.innerHTML = '<i class="fas fa-edit"></i>';
        editBtn.classList.add("interactBtn");
        editBtn.onclick = function(){
            openCategoryModal(div, id, title.textContent, desc.innerHTML, $(img).attr("src"), footerText.textContent);
        };
        editContainer.appendChild(editBtn);
    }

    parent.appendChild(div)
    if (typeof gmod !== 'undefined' && 'updateLang' in gmod) {
        gmod.updateLang();
    }
};

view.AddArticle = function(parent, id, name, description, price, image, classs, editBtn){
    var div = document.createElement("div"); 		  
    div.className  = "jumbotron content";

    var img = document.createElement("img");


    if((image && image.trim().lenght !== 0)){
        img.src = image;
        $(img).show();
    }else{
        img.src = "";
        $(img).hide();
    }

    img.align = "left";
    img.style.paddingRight = "5px";
    div.appendChild(img);

    var classInput = document.createElement("input");
    classInput.setAttribute("type", "hidden");
    $(classInput).val(classs);
    div.appendChild(classInput);
    
    var title = document.createElement("h4");
    title.className  = "title textcontent";
    title.textContent = name;
    div.appendChild(title);

    var priceP = document.createElement("p");
    priceP.className  = "textcontent";
    div.appendChild(priceP);

    var prefix = document.createElement("strong");
    prefix.setAttribute("cc-lang", "price:");
    priceP.appendChild(prefix);

    var span = document.createElement("span");
    span.textContent = price;
    priceP.appendChild(span);

    var desc = document.createElement("p");
    desc.className  = "textcontent";
    desc.innerHTML = description;
    div.appendChild(desc);

    var buyBtn = document.createElement("button");
    buyBtn.type = "button";
    buyBtn.className  = "btn btn-secondary";
    buyBtn.onclick = function(){
        openBuyModal(id, name, price);
    };
    buyBtn.setAttribute("cc-lang", "buy");
    div.appendChild(buyBtn);

    if (editBtn){
        var editContainer = document.createElement("div");
        editContainer.classList.add("editContainer");
        div.appendChild(editContainer);

        var remBtn = document.createElement("button");
        remBtn.innerHTML = '<i class="fas fa-trash-alt"></i>';
        remBtn.classList.add("interactBtn");
        var sending = false;
        remBtn.onclick = function(){
            if (sending){
                return;
            }

            showLoading();
            sending = true;

            $.post(window.location.href + "&service=rem_article", {
                id: id,
            }).done(function(data){
                if(data.status == 1){
                    $(div).fadeOut(200, function(){
                        $(this).remove();
                    });
                }else{
                    toastr.error(data.reason);
                }
                
                sending = false;
                $.modal.close();
            });
        };
        editContainer.appendChild(remBtn);

        var editBtn = document.createElement("button");
        editBtn.innerHTML = '<i class="fas fa-edit"></i>';
        editBtn.classList.add("interactBtn");
        editBtn.onclick = function(){
            openArticleModal(div, id, title.textContent, desc.innerHTML, $(img).attr("src"), $(classInput).val(), span.textContent);
        };
        editContainer.appendChild(editBtn);
    }
    
    parent.appendChild(div);
    if (typeof gmod !== 'undefined' && 'updateLang' in gmod) {
        gmod.updateLang();
    }
};

view.AddLocation = function(id){
    const div = document.createElement("option");
    div.textContent = id; // Name is the same as id ... 
    div.setAttribute("value", id);

    $("#locationSelect").append(div);
};

$(document).ready(function(){
    $("#newCatBtn").click(function(){
        openCategoryModal();
    });

    $("#newArticleBtn").click(function(){
        openArticleModal();
    });

    $("#backBtn").click(function(){
        $("#categoriesContainer").show();
        $("#articlesContainer").hide();
    });
});
