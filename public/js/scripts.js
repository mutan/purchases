/* Bootstrap Tooltips Initialization */

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

/* JQuery автодополнение */
/* http://api.jqueryui.com/autocomplete/ */

$("#basket_user_shop").autocomplete({
    minLength: 2,
    source: '/basket/autocomplete',
    select: function(event, ui) {
        $('#basket_shop').val(ui.item.value);
        //$('#basket-shop-form').submit();
    }
});

/* Basket new */

$('#basketNew').on('click', handleMainModal);

function handleMainModal(e) {
    e.preventDefault();
    let content =

        $.ajax({
            url: `/basket/product/${id}/edit`,
            type: 'POST',
            beforeSend: ()=> {toggleButtonSpinnerIcon(e);},
            complete: ()=> {toggleButtonSpinnerIcon(e);}
        }).then(function (responce) {
            reload(id, $('#modalProductEdit'), responce);
        });
}

function toggleButtonSpinnerIcon(e) {
    let icon = $(e.currentTarget).find('i');
    alert(icon.attr("class"));
    icon.toggleClass('fa-edit fa-spinner fa-spin');
}

/* Product edit */

$('.product-edit').on('click', (e) => {
    e.preventDefault();
    let id = $(e.currentTarget).attr('data-id');

    $.ajax({
        url: `/basket/product/${id}/edit`,
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
            url: `/basket/product/${id}/edit`,
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
