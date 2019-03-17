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
        if (options.size) {
            $modal.find('.modal-dialog').addClass(options.size);
        }
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

$('#basket-new').on('click', (e)=> {
    Modal.handleMainModal(e, {
        url: `/basket/new`,
        shopAutocomplete: true,
        shopAutocompleteElem: '#basket_user_shop'
    });
});

$('#basket-edit').on('click', (e)=> {
    let id = $(e.currentTarget).attr('data-basket-id');
    Modal.handleMainModal(e, {
        url: `/basket/${id}/edit`,
        shopAutocomplete: true,
        shopAutocompleteElem: '#basket_user_shop'
    });
});

$('#product-new').on('click', (e)=> {
    let basketId = $(e.currentTarget).attr('data-basket-id');
    Modal.handleMainModal(e, {
        url: `/basket/${basketId}/product/new`,
        size: 'modal-lg'
    });
});

$('.product-edit').on('click', (e)=> {
    let basketId = $(e.currentTarget).attr('data-basket-id');
    let productId = $(e.currentTarget).attr('data-product-id');
    Modal.handleMainModal(e, {
        url: `/basket/${basketId}/product/${productId}/edit`,
        size: 'modal-lg'
    });
});
