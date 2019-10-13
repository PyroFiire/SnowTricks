var numberCommentsLoadMore = 5;
var currentLimit = $('#comments .comment').length;
var button = $("#loadMore");
const MIN_COMMENTS_FOR_DISPLAY_BUTTON = 5;

if(currentLimit < MIN_COMMENTS_FOR_DISPLAY_BUTTON) {
    button.hide();
}

$(document).ready(function(){

    button.on('click', function(e) {
        e.preventDefault();
    
        $.ajax({
            url : button.attr('href'),
            type : 'POST',
            data : 'currentLimit=' + currentLimit + '&numberCommentsLoadMore=' + numberCommentsLoadMore,
            dataType : 'html', // Le type de données à recevoir, ici, du HTML.

            success : function(commentsDatas, statut){ // code_html contient le HTML renvoyé
                $('#comments').append( commentsDatas );
                currentLimit = currentLimit + numberCommentsLoadMore;
                var numberCommentsInserted = $(commentsDatas).filter('.comment').length ;
                
                if(numberCommentsInserted != numberCommentsLoadMore) {
                    button.hide();
                }
            },
            error : function(resultat, statut, erreur){
                console.log('error Ajax');
            },
        });

    });

});
