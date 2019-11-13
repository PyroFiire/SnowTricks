var inputHidden = $("#trick_mediaDelete");

$(".mediaDelete").on("click", function(e) {
    e.preventDefault();
    // hide media and take this id
    var id = $(this).attr("data-id");
    $(this).parents(".div-card").hide();

    // ad this id on the hidden field for delete
    var currentValue = inputHidden.val();
    inputHidden.attr("value", currentValue + "/" + id);
});

