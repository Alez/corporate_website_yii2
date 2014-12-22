filesModule = (function() {
    var self;

    return {
        /**
         * Запустить после создания экземпляра
         */
        init: function() {
            self = this;

            $('.fileDelete-js').on('click', function() {
                var deleteUrl = this.getAttribute('data-delete-url'),
                    id = this.getAttribute('data-delete-id'),
                    fileId = this.getAttribute('data-delete-fileid'),
                    fieldName = this.getAttribute('data-delete-field-name');

                self.deleteFile.call(this, deleteUrl, id, fileId, fieldName);
            });
        },

        /**
         * Пошлёт запрос на удаление файла у записи динамической страницы
         *
         * @param url Адрес для запроса
         * @param id Номер параметра страницы
         * @param fileId Номер файла
         * @param fieldName Название поля где хранится файл
         */
        deleteFile: function(url, id, fileId, fieldName) {
            var data = {};

            data.id = id;
            if (fileId) {
                data.fileId = fileId;
            }
            if (fieldName) {
                data.fieldName = fieldName;
            }

            $.ajax(url, {
                context: this,
                data: data,
                success: function (response) {
                    if (response) {
                        self.removeFileNode(this);
                    }
                },
                error: function () {
                    alert('Что-то пошло не так');
                },
                timeout: 3000
            });
        },

        /**
         * Удалит ноду-обёртку файла из ДОМа
         *
         * @param node Нода крестика, куда нажали что бы удалить файл
         */
        removeFileNode: function(node) {
            var parentNode = node.parentNode;

            $(parentNode).fadeOut(function () {
                parentNode.parentNode.removeChild(parentNode);
            });
        }
    }
})();