/* JQuery автодополнение */
/* http://api.jqueryui.com/autocomplete/ */

/* Автодополнение поля поиска карты для колоды */

$("#basket_shop").autocomplete({
    minLength: 2,
    source: '/basket/autocomplete',
    select: function(event, ui) {
        $('#basket_shop').val(ui.item.value);
        //$('#basket-shop-form').submit();
    }
});