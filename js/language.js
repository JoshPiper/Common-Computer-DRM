ComComp = (function(){
    var self = {};

    self.Lang = function(id, text){
        var sel = document.querySelectorAll("[cc-lang='" + id + "']");
        for(var i = 0; i < sel.length; i++) {
            sel[i].innerHTML = text;
        }
    };

    return self;
})();