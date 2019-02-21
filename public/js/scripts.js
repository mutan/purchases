/* Bootstrap Tooltips Initialization */

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

/* JQuery автодополнение */
/* http://api.jqueryui.com/autocomplete/ */

$("#basket_shop").autocomplete({
    minLength: 2,
    source: '/basket/autocomplete',
    select: function(event, ui) {
        $('#basket_shop').val(ui.item.value);
        //$('#basket-shop-form').submit();
    }
});

/* Product edit */

$('.product-edit').on('click', (e) => {
    e.preventDefault();
    let id = $(e.currentTarget).attr('data-id');

    $.ajax({
        url: `/product/${id}/editform`,
        type: 'POST',
        beforeSend: ()=> {toggleEditSpinnerIcon(e);},
        complete: ()=> {toggleEditSpinnerIcon(e);}
    }).then(function (responce) {
        reload(id, $('#modalProductEdit'), responce);
    });
});

function reload(id, $modal, responce) {
    $modal.find('.modal-content').html(responce.output);
    $modal.modal('show');

    $modal.find('form').on('submit', (e)=> {
        e.preventDefault();
        let formData = $(e.currentTarget).serialize();
        const $submitButton = $(e.currentTarget).find('button[type=submit]');
        $.ajax({
            url: `/product/${id}/editform`,
            type: 'POST',
            data: formData,
            beforeSend: ()=> {
                $submitButton.prop('disabled', true).toggleClass('btn-dark-green btn-danger').html("<i class='fa fa-spinner fa-spin pr-1'></i> Идет сохранение");
            }
        }).then(function (responce) {
            $submitButton.toggleClass('btn-dark-green btn-danger').html("Сохранено");
            (responce.reload) ? location.reload() : reload(id, $modal, responce);
        });
    });
}

function toggleEditSpinnerIcon(e) {
    $(e.currentTarget).find('i').toggleClass('fa-edit fa-spinner fa-spin');
}
