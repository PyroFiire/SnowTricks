//Display:none the basique input
$("#trick_fileSpotlightPicturePath").parent().addClass('d-none');

// clic button for upload file
$("#spotlightFileUpload").on('click', function(e){
    $('#trick_fileSpotlightPicturePath').click();
    return false;
});

//for delete current file if he was uploaded
var defaultSpotlight = '/picture/snowboarding-3554x1999.jpg';
//var defaultSpotlight = $('#spotlight_picture').attr('src');

// clic button for reset the file input
$("#spotlightFileDelete").on('click', function(e){
    $("#trick_fileSpotlightPicturePath")
        .attr('type', 'text')
        .attr('type', 'file');
    $('#spotlight_picture')
        .attr('src', defaultSpotlight);
    return false;
});

// preview image if is changed input
$("#trick_fileSpotlightPicturePath").on('change', function(){
    readURL(this);
});

//preview the upload file
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#spotlight_picture')
                .attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }
}
