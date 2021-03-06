const MIN_TRICKS_FOR_DISPLAY_BUTTON_UP = 9;
var numberTricks = $("#tricks .card").length;

//return bool
var scroolExistInUrlParam = new URLSearchParams(window.location.search).has("scrool");

// function qui défile vers l'ancre donnée
function scroolTo(ancre){
    $([document.documentElement, document.body]).animate({
        scrollTop: ancre.offset().top
    }, 1500);
}

//si vrai, scrool jusqu'aux tricks
if(scroolExistInUrlParam){
    scroolTo($("#tricks"));
}

// ajoute l'evenement click aux deux boutons
$("#upToTricks, #downToTricks").on("click", function(e) {
    e.preventDefault();
    scroolTo($("#tricks"));
});

// affiche ou pas le bouton up suivant le nombre de tricks
if(numberTricks < MIN_TRICKS_FOR_DISPLAY_BUTTON_UP) {
    $("#upToTricks").hide();
}

