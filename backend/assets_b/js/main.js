pagesModule.init();
adminModule.init();
filesModule.init();

// Инициализация дэйтпикера
//document.addEventListener('DOMContentLoaded', function() {
//    $('.datepicker-js').datepicker({
//        language: 'ru',
//        format: 'dd-mm-yy'
//    });
//});

//
document.addEventListener('DOMContentLoaded', function() {
    pagesModule.templateReload('.chooseTemplate-js', '#templateFields');
});