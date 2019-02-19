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
            $.ajax({
                url: `/product/${id}/editform`,
                type: 'POST',
                data: formData,
                beforeSend: (xhr)=> {
                    $(event.currentTarget).find('button[type=submit]').find('i').toggleClass('fa-edit fa-spinner fa-spin');
                    $(event.currentTarget).find('button[type=submit]').prop('disabled', true);
                },
                complete: ()=> {
                    $(event.currentTarget).find('button[type=submit]').find('i').toggleClass('fa-edit fa-spinner fa-spin');
                }
            }).then(function (responce) {
                $('#modalProductEdit').find('.modal-content').html(responce.output);
                $('#modalProductEdit').modal('show');
                $('#modalProductEdit').on('hidden.bs.modal', function () {
                    location.reload();
                })
            });
        });

    });
});


