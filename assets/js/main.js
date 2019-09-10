/* CSS */

require('../css/main.scss');


/* JS LIBRARIES */

const $ = require('jquery');
//global.$ = global.jQuery = $;
require('jquery-ui/themes/base/core.css');
require('jquery-ui/themes/base/theme.css');
require('jquery-ui/themes/base/autocomplete.css');
require('jquery-ui/ui/core');
require('jquery-ui/ui/widgets/autocomplete');

//Если включить, теги i будут заменяться на svg
//require('@fortawesome/fontawesome-free/js/all');

require('popper.js/dist/popper');
require('bootstrap/dist/js/bootstrap');

require('datatables.net/js/jquery.dataTables');
require('datatables.net-bs4/js/dataTables.bootstrap4');


/* JS CUSTOM */

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
            source: '/order/shop/autocomplete',
            select: function(event, ui) {
                $(id).val(ui.item.value);
                //$('#order-shop-form').submit();
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
        }).then(function (response) {
            Modal.reload(Modal.getModal(), response, options);
        });
    },

    reload: function($modal, response, options) {
        if (options.size) {
            $modal.find('.modal-dialog').addClass(options.size);
        }
        $modal.find('.modal-content').html(response.output);
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
            }).then(function (response) {
                (response.reload) ? location.reload() : Modal.reload($modal, response, options);
            });
        });
    }
};

let Ajax = {
     send: function(e, url, confirmText, buttonText) {
         if (confirm(confirmText)) {
             e.preventDefault();
             $.ajax({
                 url: url,
                 type: 'POST',
                 beforeSend: ()=> {
                     $(e.currentTarget).prop('disabled', true).html(`<i class='fa fa-spinner fa-spin pr-1'></i> ${buttonText}`);
                 },
             }).then(function () {
                 location.reload();
             });
         }
     },
};

$('#order_set_redeemed').on('click', (e)=> {
    let orderId = $(e.currentTarget).attr('data-id');
    Ajax.send(e, `/order/${orderId}/set_redeemed`, "Действительно начать выкуп?", "Меняем статус");
});
$('#order_approve').on('click', (e)=> {
    let orderId = $(e.currentTarget).attr('data-id');
    Ajax.send(e, `/order/${orderId}/approve`, "Действительно утвердить?", "Утверждаем");
});
$('#order_return_to_new').on('click', (e)=> {
    let orderId = $(e.currentTarget).attr('data-id');
    Ajax.send(e, `/order/${orderId}/return_to_new`, "Действительно вернуть в статус Новый?", "Возвращаем");
});

$('.order-manager-edit').on('click', (e)=> {
    let orderId = $(e.currentTarget).attr('data-id');
    Modal.handleMainModal(e, {
        url: `/manager/order/${orderId}/edit`,
        size: 'modal-lg'
    });
});
$('.product-manager-edit').on('click', (e)=> {
    let productId = $(e.currentTarget).attr('data-id');
    Modal.handleMainModal(e, {
        url: `/manager/product/${productId}/edit`,
        size: 'modal-lg',
        shopAutocomplete: true,
        shopAutocompleteElem: '#product_manager_purchaseShop' // элемент, на который вешаем autocomplete
    });
});
$('#product-new').on('click', (e)=> {
    let orderId = $(e.currentTarget).attr('data-id');
    Modal.handleMainModal(e, {
        url: `/order/${orderId}/product/new`,
        size: 'modal-lg'
    });
});
$('.product-edit').on('click', (e)=> {
    let productId = $(e.currentTarget).attr('data-id');
    Modal.handleMainModal(e, {
        url: `/order/product/${productId}/edit`,
        size: 'modal-lg'
    });
});
$('#order_new').on('click', (e)=> {
    Modal.handleMainModal(e, {
        url: `/order/new`,
        shopAutocomplete: true,
        shopAutocompleteElem: '#order_shop' // элемент, на который вешаем autocomplete
    });
});
$('#order_edit').on('click', (e)=> {
    let id = $(e.currentTarget).attr('data-id');
    Modal.handleMainModal(e, {
        url: `/order/${id}/edit`,
        shopAutocomplete: true,
        shopAutocompleteElem: '#order_shop'  // элемент, на который вешаем autocomplete
    });
});
$('#user_profile_edit').on('click', (e)=> {
    Modal.handleMainModal(e, {
        url: `/user/profile/edit`
    });
});
$('#user_address_new').on('click', (e)=> {
    Modal.handleMainModal(e, {
        url: `/user/address/new`,
        size: 'modal-lg'
    });
});
$('.user_address_edit').on('click', (e)=> {
    let id = $(e.currentTarget).attr('data-id');
    Modal.handleMainModal(e, {
        url: `/user/address/${id}/edit`,
        size: 'modal-lg'
    });
});
$('#user_passport_new').on('click', (e)=> {
    Modal.handleMainModal(e, {
        url: `/user/passport/new`,
        size: 'modal-lg'
    });
});
$('.user_passport_edit').on('click', (e)=> {
    let id = $(e.currentTarget).attr('data-id');
    Modal.handleMainModal(e, {
        url: `/user/passport/${id}/edit`,
        size: 'modal-lg'
    });
});