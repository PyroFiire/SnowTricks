var $collectionHolder;

// setup an "add a tag" link
var $addMediaButton = $('<button type="button" class="add_media_link" onclick="addMediaForm()">Add a media</button>');
var $newLinkLi = $('<li></li>').append($addMediaButton);

//Execute le code lorque la page a fini d'être chargé
jQuery(document).ready(function() {

    // récupère le ul qui a la class="medias"
    $collectionHolder = $('ul.medias');
    
    //ajoute un bouton de suppresion à la fin de chaque li
    $collectionHolder.find('li').each(function() { //each() permet de boucler une function sur une collection d'objets jQuery
        addMediaFormDeleteLink($(this));
    });

    //ajoute un bouton d'ajout d'un medias à la fin de la collection (ul)
    $collectionHolder.append($newLinkLi); //append() ajoute du contenu à la fin à chaque elements selectionné (ici un seul ul)

    //stock en data dans la collection le nombre d'input
    console.log($collectionHolder);
    $collectionHolder.data('index', $collectionHolder.find(':input').length); //data('cle','valeur') permet de stocké une donnée

    //evenement click qui ajoute un nouveau media
    $addMediaButton.on('click', function(e) {
        // add a new media form (see next code block)
        addMediaForm($collectionHolder, $newLinkLi);
    });
});



function addMediaForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype fabriquer par MediaType en PHP
    var prototype = $collectionHolder.data('prototype'); //data('cle') permet de recupérer une donnée

    // recupère le nombre de medias actuellement affiché
    var index = $collectionHolder.data('index');

    var newForm = prototype;

    // Remplace '__name__' dans le prototype's basé sur le nombre de medias actuel
    newForm = newForm.replace(/__name__/g, index); // (g)expliquation : Pour remplacer toutes les occurrences d'une valeur spécifiée, utilisez le modificateur global (g) 

    /* You need this only if you didn't set 'label' => false in your tags field in TaskType */
    // newForm = newForm.replace(/__name__label__/g, index);
    
    // Ajoute plus un à l'index pour l'ahout du prochain media
    $collectionHolder.data('index', index + 1);

    // Ajoute une balise <li> autour du prototype(newForm)
    var $newFormLi = $('<li></li>').append(newForm);
    // Ajoute ce <li> juste avant le bouton "add a media"(newLinkLi)
    $newLinkLi.before($newFormLi);

    // Ajoute un lien de suppression au nouveau prototype inséré(newFormLi)
    addMediaFormDeleteLink($newFormLi);
}



function addMediaFormDeleteLink($mediaFormLi) {
    var $removeFormButton = $('<button type="button">Delete this media</button>');
    $mediaFormLi.append($removeFormButton);

    $removeFormButton.on('click', function(e) {
        // remove the li for the tag form
        $mediaFormLi.remove();
    });
}