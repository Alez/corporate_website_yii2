filesModule = (function() {
    'use strict';
    var self,

    /**
     * Удалит ноду-обёртку файла из ДОМа
     *
     * @param node Нода крестика, куда нажали что бы удалить файл
     */
    removeFileNode = function(node) {
        var parentNode = node.parentNode;

        $(parentNode).fadeOut(function () {
            parentNode.parentNode.removeChild(parentNode);
        });
    };

    return {
        /**
         * Запустить после создания экземпляра
         */
        init: function() {
            self = this;

            var fileDelete = document.querySelector('.fileDelete-js');

            if (fileDelete) {
                fileDelete.addEventListener('click', function() {
                    var paramsArray = [].filter.call(this.attributes, function(el) {
                            return /^data-/.test(el.name);
                        }),
                        params = {};

                    [].forEach.call(paramsArray, function(attr) {
                        // Отрежем ненужный префикс 'data-'
                        var name = attr.name.substr(5, attr.name.length); // 5 - 'data-'.length
                        params[name] = attr.value;
                    });

                    self.deleteFile.call(this, params);
                });
            }
        },

        /**
         * Пошлёт запрос на удаление файла у записи динамической страницы
         *
         * @param params Параметры удаляемого файла
         */
        deleteFile: function(params) {
            var url = params.url;
            delete params.url;

            $.ajax(url, {
                context: this,
                data: params,
                success: function (response) {
                    if (response) {
                        this.removeFileNode(this);
                    }
                },
                error: function () {
                    alert('Что-то пошло не так');
                },
                timeout: 3000
            });
        }
    }
})();