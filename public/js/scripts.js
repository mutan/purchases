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
    $.get(`/product/${id}/editform`).then(function (responce) {
        $('#modalProductEdit').find('.modal-content').html(responce.output);
        $('#modalProductEdit').modal('show');

        $('button.form_save').on('click', (event) => {
            event.preventDefault();

            let formData = $(event.currentTarget).closest('form').serialize();

            alert(formData);

            /*$.post(`/product/${id}/editform`, formData).then(function (responce) {
                $('#modalProductEdit').find('.modal-content').html(responce.output);
                $('#modalProductEdit').modal('show');
            });*/

            //console.dir(responce.output);
        });
    });
});


