pagesModule = (function() {
    var self;

    return {
        /**
         * Запустить после создания экземпляра
         */
        init: function() {
            self = this;

            // Инициализация механизма перезагрузка шаблонов страниц
            pagesModule.templateReload('.chooseTemplate-js', '#templateFields');
        },

        /**
         * Добавить событие для перезагрузки шаблона страницы
         *
         * @param switcherSelector Селектор селекта с шаблонами
         * @param containerSelector Селектор контейнера шаблона
         */
        templateReload: function(switcherSelector, containerSelector) {
            $(document.body).on('change', switcherSelector, function () {
                $.pjax({
                    url: this.getAttribute('data-url') + this.value,
                    container: containerSelector
                });
            })
        }
    }
})();