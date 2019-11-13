var inputFile = $("#trick_spotlightPicturePath_file");
var inputDeleteSpotlight = $("#trick_spotlightDelete")

//preview the upload file
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $("#spotlight_picture")
                .attr("src", e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }
}

//Display:none the basique input
inputFile.parent().addClass("d-none");

// clic button for upload file
$("#spotlightFileUpload").on("click", function(e){
    e.preventDefault();
    inputFile.click();
});

//for delete current file if he was uploaded
var defaultSpotlight = "/picture/defaultSpotlightPicture.jpg";
//var defaultSpotlight = $('#spotlight_picture').attr('src');

// clic button for reset the file input
$("#spotlightFileDelete").on("click", function(e){
    e.preventDefault();
    inputFile
        .attr("type", "text")
        .attr("type", "file");
    $("#spotlight_picture")
        .attr("src", defaultSpotlight);

    inputDeleteSpotlight.attr("value", true);
});

// preview image if is changed input
inputFile.on("change", function(){
    readURL(this);
});


