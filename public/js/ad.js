$('#add-image').click(function(){
    //Récupération du numéro des futurs champs que je vais créer
    const index = $('#widgets-counter').val();
    $('#widgets-counter').val(Number(index)+1);
    //récupération du prototype des entrées
    const tmpl = $('#annonce_images').data('prototype').replace(/__name__/g, index);
    //Injection du code au sein de la div
    $('#annonce_images').append(tmpl);
    // Gestion du bouton supprimé
    handleDeleteButtons();
});

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounter(){
    const count = +$('#annonce_images div.form-group').length;
    $('#widgets-counter').val(count);
}

updateCounter();
handleDeleteButtons();