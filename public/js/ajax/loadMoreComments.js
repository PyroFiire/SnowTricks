var numberCommentsLoadMore = 5;
var currentLimit = $('#comments .comment').length;
var buttonLoadMore = $("#loadMore");
const MIN_COMMENTS_FOR_DISPLAY_BUTTON = 5;

if(currentLimit < MIN_COMMENTS_FOR_DISPLAY_BUTTON) {
    buttonLoadMore.hide();
}

$(document).ready(function(){

    buttonLoadMore.on('click', function(e) {
        e.preventDefault();
    
        $.ajax({
            url : buttonLoadMore.attr('href'),
            type : 'POST',
            data : 'currentLimit=' + currentLimit + '&numberCommentsLoadMore=' + numberCommentsLoadMore,
            dataType : 'html', // Le type de données à recevoir, ici, du HTML.

            success : function(commentsDatas, statut){ // code_html contient le HTML renvoyé
                $('#comments').append( commentsDatas );
                currentLimit = currentLimit + numberCommentsLoadMore;
                var numberCommentsInserted = $(commentsDatas).filter('.comment').length ;
                
                if(numberCommentsInserted != numberCommentsLoadMore) {
                    buttonLoadMore.hide();
                }
            },
            error : function(resultat, statut, erreur){
                console.log('error Ajax');
            },
        });

    });

});
