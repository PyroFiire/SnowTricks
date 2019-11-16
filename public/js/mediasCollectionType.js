var $collectionHolder;

// setup an "add a tag" link
var $addVideoButton = $("<button type=\"button\" class=\"add_video_link\" >Add a video</button>");
$addVideoButton.addClass("btn btn-primary");
var $newVideoLinkLi = $("<li></li>").append($addVideoButton);
$newVideoLinkLi.attr("id", "add_video_li").addClass("col-12");

var $addPictureButton = $("<button type=\"button\" class=\"add_picture_link\">Add a picture</button>");
$addPictureButton.addClass("btn btn-primary");
var $newPictureLinkLi = $("<li></li>").append($addPictureButton);
$newPictureLinkLi.attr("id", "add_video_link").addClass("col-12");

function addMediaForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype fabriquer par MediaType en PHP
    var prototype = $collectionHolder.data("prototype"); //data('cle') permet de recupérer une donnée

    // recupère le nombre de medias actuellement affiché
    var index = $collectionHolder.data("index");

    var newForm = prototype;

    // Remplace '__name__' dans le prototype's basé sur le nombre de medias actuel
    newForm = newForm.replace(/__name__/g, index); // (g)expliquation : Pour remplacer toutes les occurrences d'une valeur spécifiée, utilisez le modificateur global (g) 
    
    // Ajoute plus un à l'index pour l'ajout du prochain media
    $collectionHolder.data("index", index + 1);

    // Ajoute une balise <li> autour du prototype(newForm)
    var $newFormLi = $("<li class=\"col-12 col-md-11 col-lg-10 col-xl-7 d-flex justify-content-around\"></li>").append(newForm);

    // Ajoute ce <li> juste avant le bouton "add a media"(newLinkLi)
    $newLinkLi.before($newFormLi);

    // Ajoute un lien de suppression au nouveau prototype inséré(newFormLi)
    addMediaFormDeleteLink($newFormLi);
}

function addMediaFormDeleteLink($mediaFormLi) {
    var $removeFormButton = $("<button type=\"button\">Delete</button>");
    $removeFormButton.addClass("btn btn-danger");
    $mediaFormLi.append($removeFormButton);

    $removeFormButton.on("click", function(e) {
        // remove the li for the tag form
        $mediaFormLi.remove();
    });
}

//Execute le code lorque la page a fini d'être chargé
jQuery(document).ready(function() {

    // récupère le ul qui a la class="medias"
    var $collectionVideoHolder = $("ul.videos");
    var $collectionPictureHolder = $("ul.pictures");
    
    //ajoute un bouton de suppresion à la fin de chaque li
    $collectionVideoHolder.find("li").each(function() { //each() permet de boucler une function sur une collection d'objets jQuery
        addMediaFormDeleteLink($(this));
    });
    $collectionPictureHolder.find("li").each(function() { //each() permet de boucler une function sur une collection d'objets jQuery
        addMediaFormDeleteLink($(this));
    });

    //ajoute un bouton d'ajout d'un medias à la fin de la collection (ul)
    $collectionVideoHolder.append($newVideoLinkLi); //append() ajoute du contenu à la fin à chaque elements selectionné (ici un seul ul)
    $collectionPictureHolder.append($newPictureLinkLi); //append() ajoute du contenu à la fin à chaque elements selectionné (ici un seul ul)

    //stock en data dans la collection le nombre d'input
    $collectionVideoHolder.data("index", $collectionVideoHolder.find(":input").length); //data('cle','valeur') permet de stocké une donnée
    $collectionPictureHolder.data("index", $collectionPictureHolder.find(":input").length); //data('cle','valeur') permet de stocké une donnée

    //evenement click qui ajoute un nouveau media
    $addVideoButton.on("click", function(e) {
        // add a new media form (see next code block)
        addMediaForm($collectionVideoHolder, $newVideoLinkLi);
    });
    $addPictureButton.on("click", function(e) {
        // add a new media form (see next code block)
        addMediaForm($collectionPictureHolder, $newPictureLinkLi);
        bsCustomFileInput.init();
    });
});
