/* JS LIBRARIES */

const $  = require('jquery');
require('datatables.net-bs4');
require('bootstrap');
require('@fortawesome/fontawesome-free/js/all');

/* CSS */

require('../css/app.scss');

/* CUSTOM JS */

$(document).ready(() => {

    /* DataTables */
    $('#data-table').DataTable();

    /* Вывод изображения при наведении на название-ссылку */
    $(function () {
        $('[data-toggle="image-popover"]').popover({
            trigger: 'hover',
            placement: 'right',
            html: true,
            boundary: 'window',
            fallbackPlacement: 'clockwise',
            container: 'body',
        })
    });

});
