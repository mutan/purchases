/* JS LIBRARIES */

const $  = require('jquery');
//require('@fortawesome/fontawesome-free/js/all');
//require('datatables.net-bs4');
//require('bootstrap');
//require('popper.js');
//require('jquery.easing/jquery.easing');
//require('jquery-localize/dist/jquery.localize');
//require('screenfull/dist/screenfull.js');

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
