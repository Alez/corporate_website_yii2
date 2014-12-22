document.addEventListener('DOMContentLoaded', function() {
    /*****************************************************
    СКРИПТЫ ВСПЛЫВАЮЩЕГО ОКОШКА С РЕДАКТИРОВАНИЕМ
     ****************************************************/
    var $galleryList = $('.galleryList-js');

    // Ищем кнопки во всплывающем окне, которые позволяют обрезать фотографии, вешаем обработчики
    var wideBtnSelector = '.cropper-run-wide-js',
        squareBtnSelector = '.cropper-run-square-js',
        releaseBtnSelector = '.cropper-run-release-js',
        destroyBtnSelector = '.cropper-run-destroy-js',
        popup = document.getElementById('editPhotoPopup'),
        runCropper = function(proportion) {
            if (typeof(proportion) === 'number') {
                $('.cropper-js').cropper('setAspectRatio', proportion);
            } else {
                $('.cropper-js').cropper('destroy').cropper('release');
            }

            $(destroyBtnSelector).fadeIn('fast');
        };

    // Вызывает всплывающее окошко редактирования параметров фотографии
    $galleryList.on('click', 'img', function () {
        var url = this.getAttribute('data-edit-url'),
            id = this.parentNode.getAttribute('data-id');

        if (popup) {
            $.ajax(url, {
                data: {
                    id: id
                },
                success: function(response) {
                    if (response) {
                        if (popup) {
                            $(popup).html(response.trim());
                            $(popup).modal('show');
                        }
                    }
                },
                error: function() {
                    alert('Что-то пошло не так');
                },
                timeout: 3000,
                beforeSend: function() {
                    /**/
                },
                complete: function() {
                    /**/
                }
            });
        }
    });

    // Вызывает удаление фотки
    //$galleryList.on('click', 'i', function() {
    //    var url = this.getAttribute('data-delete-url'),
    //        parentNode = this.parentNode,
    //        id = parentNode.getAttribute('data-id'),
    //        elementId = parentNode.getAttribute('data-product-id'),
    //        type = parentNode.getAttribute('data-type');
    //
    //    $.ajax(url, {
    //        data: {
    //            productId: elementId,
    //            fileId: id,
    //            type: type
    //        },
    //        success: function(response) {
    //            if (response) {
    //                $(parentNode).fadeOut(function() {
    //                    parentNode.parentNode.removeChild(parentNode);
    //                });
    //            }
    //        },
    //        error: function() {
    //            alert('Что-то пошло не так');
    //        },
    //        timeout: 3000,
    //        beforeSend: function() {
    //            /**/
    //        },
    //        complete: function() {
    //            /**/
    //        }
    //    });
    //});

    $(document.body).on('click', wideBtnSelector, function() {
        runCropper(16/9);
    });
    $(document.body).on('click', squareBtnSelector, function() {
        runCropper(5/4);
    });
    $(document.body).on('click', releaseBtnSelector, function() {
        runCropper('release');
    });
    $(document.body).on('click', destroyBtnSelector, function(e) {
        if ($(e.target).is(':visible')) {
            $(e.target).hide();
            $('.cropper-js').cropper('destroy');
        }
    });

    // Собираем инфу об обрезанном фото перед сабмитом всплывающего окошка
    $(document.body).on('submit', '#editImagePopupForm', function(e) {
        e.preventDefault();

        var cropData = document.getElementById('cropData');

        if ($(destroyBtnSelector).is(':visible')) {
            cropData.value = JSON.stringify($('.cropper-js').cropper('getData'));
        } else {
            cropData.value = '';
        }

        $.ajax(e.target.action, {
            data: $(e.target).serialize(),
            type: 'POST',
            success: function(response) {
                if (response) {
                    var $updatedImage = $galleryList.find('li[data-id=' + response.imageId + ']').find('img');
                    $updatedImage.fadeOut('slow', function() {
                        $updatedImage.prop('src', response.imageSrc + '?time=' + Date.now());
                        $updatedImage.fadeIn('slow');
                    });
                    $(popup).modal('hide');
                }
            },
            error: function() {
                alert('Что-то пошло не так');
            },
            timeout: 3000
        });


    });
});