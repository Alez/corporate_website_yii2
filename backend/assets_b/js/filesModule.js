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
        },

        /**
         * Соберёт все параметры из data атрибутов из ноды отвечающей за удаление
         *
         * @param node
         * @returns {{}}
         */
        gatherDeleteParams = function(node) {
            var paramsArray = [].filter.call(node.attributes, function(el) {
                    return /^data-/.test(el.name);
                }),
                params = {};

            [].forEach.call(paramsArray, function(attr) {
                // Отрежем ненужный префикс 'data-'
                // 5 - 'data-'.length
                params[attr.name.substr(5, attr.name.length)] = attr.value;
            });

            return params;
        };

    return {
        /**
         * Запустить после создания экземпляра
         */
        init: function() {
            self = this;

            var fileDelete = document.querySelectorAll('.fileDelete-js');
            if (fileDelete) {
                [].forEach.call(fileDelete, function(el) {
                    el.addEventListener('click', function() {
                        self.deleteFile.call(this, gatherDeleteParams(el));
                    });
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
                        removeFileNode(this);
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