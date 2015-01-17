adminModule = (function() {
    var self;

    return {
        /**
         * Запустить после создания экземпляра
         */
        init: function() {
            self = this;

            // Инициализация дэйтпикера
            self.datePicker('.datepicker-js');
        },

        /**
         * Инициализация дейтпикера
         * @param selector
         */
        datePicker: function (selector) {
            $(selector).datepicker({
                language: 'ru',
                format: 'dd-mm-yy'
            });
        }
    }
})();