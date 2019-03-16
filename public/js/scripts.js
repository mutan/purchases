/* Bootstrap Tooltips Initialization */

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

/* Modal */

let Modal = {
    getModal: function() {
        return $('#modalMain');
    },

    toggleButtonSpinnerIcon: function(e) {
        $(e.currentTarget).find('i').toggleClass('fa-spinner fa-spin');
    },

    /* JQuery автодополнение */
    /* http://api.jqueryui.com/autocomplete/ */
    shopAutocomplete: function(id) {
        $(id).autocomplete({
            minLength: 2,
            source: '/basket/shop/autocomplete',
            select: function(event, ui) {
                $(id).val(ui.item.value);
                //$('#basket-shop-form').submit();
            }
        });
    },

    handleMainModal: function(e, options) {
        e.preventDefault();
        $.ajax({
            url: options.url,
            type: 'POST',
            beforeSend: ()=> {Modal.toggleButtonSpinnerIcon(e);},
            complete: ()=> {Modal.toggleButtonSpinnerIcon(e);}
        }).then(function (responce) {
            Modal.reload(Modal.getModal(), responce, options);
        });
    },

    reload: function($modal, responce, options) {
        $modal.find('.modal-content').html(responce.output);
        $modal.modal('show');
        if (options.shopAutocomplete) {
            Modal.shopAutocomplete(options.shopAutocompleteElem);
        }

        $modal.find('form').on('submit', (e)=> {
            e.preventDefault();
            let formData = $(e.currentTarget).serialize();
            const $submitButton = $(e.currentTarget).find('button[type=submit]');
            $.ajax({
                url: options.url,
                type: 'POST',
                data: formData,
                beforeSend: ()=> {
                    $submitButton.prop('disabled', true).html("<i class='fa fa-spinner fa-spin pr-1'></i> Идет сохранение");
                }
            }).then(function (responce) {
                (responce.reload) ? location.reload() : Modal.reload($modal, responce, options);
            });
        });
    }
};

$('#basketNew').on('click', (e)=> {
    Modal.handleMainModal(e, {
        url: `/basket/new`,
        shopAutocomplete: true,
        shopAutocompleteElem: '#basket_user_shop'
    });
});

$('#basketEdit').on('click', (e)=> {
    let id = $(e.currentTarget).attr('data-id');
    Modal.handleMainModal(e, {
        url: `/basket/${id}/edit`,
        shopAutocomplete: true,
        shopAutocompleteElem: '#basket_user_shop'
    });
});





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
