var numberTricksLoadMore = 4;
var currentLimit = $("#tricks .card").length;
var button = $("#loadMore");

$(document).ready(function(){

    button.on("click", function(e) {
        e.preventDefault();
    
        $.ajax({
            url : button.attr("href"),
            type : "POST",
            data : "currentLimit=" + currentLimit + "&numberTricksLoadMore=" + numberTricksLoadMore,
            dataType : "html", // Le type de données à recevoir, ici, du HTML.

            success : function(tricksDatas, statut){ // code_html contient le HTML renvoyé
                $("#tricks > .row").append( tricksDatas );
                currentLimit = currentLimit + numberTricksLoadMore;
                var numberTricksInserted = $(tricksDatas).filter(".div-card").length ;

                if(numberTricksInserted !== numberTricksLoadMore) {
                    button.hide();
                }
                if(currentLimit > MIN_TRICKS_FOR_DISPLAY_BUTTON_UP) {
                    $("#upToTricks").show();
                }
            },
            error : function(resultat, statut, erreur){
                
            },
        });

    });

});
