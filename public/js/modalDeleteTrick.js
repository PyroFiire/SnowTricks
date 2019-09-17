$('#modalDeleteTrick').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var title = button.data('title') // Extract info from data-* attributes
    var slug = button.data('slug') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    var modal = $(this)
    modal.find('.modal-body').text('Are you sure to remove \"' + title + '\"')
    var modal2 = $(this)
    modal2.find('.modal-footer a').attr('href', '/tricks/delete/' + slug)
})