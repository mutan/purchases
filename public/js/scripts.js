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

$('.product-edit').on('click', (event) => {
    event.preventDefault();
    let id = $(event.currentTarget).attr('data-id');

    $.ajax({
        url: `/product/${id}/editform`,
        type: 'POST',
        beforeSend: (xhr)=> {
            $(event.currentTarget).find('i').toggleClass('fa-edit fa-spinner fa-spin');
        },
        complete: ()=> {
            $(event.currentTarget).find('i').toggleClass('fa-edit fa-spinner fa-spin');
        }
    }).then(function (responce) {
        $('#modalProductEdit').find('.modal-content').html(responce.output);
        $('#modalProductEdit').modal('show');

        $('#modalProductEdit').find('form').on('submit', (event)=> {
            event.preventDefault();
            let formData = $(event.currentTarget).serialize();
            const $submitButton = $(event.currentTarget).find('button[type=submit]');
            reload(id, formData, $submitButton);
        });

    });
});

function reload(id, formData, $submitButton) {
    $.ajax({
        url: `/product/${id}/editform`,
        type: 'POST',
        data: formData,
        beforeSend: (xhr)=> {
            $submitButton.find('i').toggleClass('fa-edit fa-spinner fa-spin');
            $submitButton.prop('disabled', true).toggleClass('btn-dark-green btn-danger').html('Идет сохранение...');
        },
        complete: ()=> {
            // на самом деле не успеет сработать, т.к. then сработает раньше
            $submitButton.find('i').toggleClass('fa-edit fa-spinner fa-spin');
            $submitButton.prop('disabled', true).toggleClass('btn-primary btn-danger');
        }
    }).then(function (responce) {
        alert(1);
        sleep(3000);
        if (responce.reload) {
            location.reload();
        }
        $('#modalProductEdit').find('.modal-content').html(responce.output);
        //$('#modalProductEdit').modal('show');

        $('#modalProductEdit').find('form').on('submit', (event)=> {
            event.preventDefault();
            let formData = $(event.currentTarget).serialize();
            const $submitButton = $(event.currentTarget).find('button[type=submit]');
            reload(id, formData, $submitButton);
        });
    });
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

$('#modalProductEdit').on('hidden.bs.modal', function () {
    location.reload();
})