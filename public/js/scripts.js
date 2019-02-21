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

function toggleEditSpinnerIcon(e) {
    $(e.currentTarget).find('i').toggleClass('fa-edit fa-spinner fa-spin');
}

$('.product-edit').on('click', (e) => {
    e.preventDefault();
    let id = $(e.currentTarget).attr('data-id');

    $.ajax({
        url: `/product/${id}/editform`,
        type: 'POST',
        beforeSend: ()=> {toggleEditSpinnerIcon(e);},
        complete: ()=> {toggleEditSpinnerIcon(e);}
    }).then(function (responce) {
        $('#modalProductEdit').find('.modal-content').html(responce.output);
        $('#modalProductEdit').modal('show');

        $('#modalProductEdit').find('form').on('submit', (e)=> {
            reload(id, e);
        });
    });
});

function reload(id, e) {
    e.preventDefault();
    let formData = $(e.currentTarget).serialize();
    const $submitButton = $(e.currentTarget).find('button[type=submit]');
    $.ajax({
        url: `/product/${id}/editform`,
        type: 'POST',
        data: formData,
        beforeSend: ()=> {
            $submitButton.find('i').toggleClass('fa-edit fa-spinner fa-spin');
            $submitButton.prop('disabled', true).toggleClass('btn-dark-green btn-danger').html('Идет сохранение...');
        },
        complete: ()=> {
            // на самом деле не успеет сработать, т.к. then сработает раньше
            $submitButton.find('i').toggleClass('fa-edit fa-spinner fa-spin');
            $submitButton.prop('disabled', true).toggleClass('btn-primary btn-danger');
        }
    }).then(function (responce) {
        sleep(3000);
        if (responce.reload) {
            location.reload();
        }
        $('#modalProductEdit').find('.modal-content').html(responce.output);
        //$('#modalProductEdit').modal('show');

        $('#modalProductEdit').find('form').on('submit', (e)=> {
            reload(id, e);
        });
    });
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

/*$('#modalProductEdit').on('hidden.bs.modal', function () {
    location.reload();
})*/
