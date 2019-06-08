/* JS LIBRARIES */

// const $  = require('jquery');
// require('jquery.easing/jquery.easing');
// require('jquery-localize/dist/jquery.localize');
// require('popper.js');
// require('bootstrap');

/*require('./modernizr.custom.js');
const $  = require('jquery');
require('popper.js');
require('bootstrap');
require('./js.storage.js');
require('jquery.easing/jquery.easing');
require('./animo.js');
require('screenfull');
require('jquery-localize/dist/jquery.localize');
require('./angle.js');*/

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
