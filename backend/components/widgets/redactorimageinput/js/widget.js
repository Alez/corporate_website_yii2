// Добавление к Редактору загруженных фотографий
document.addEventListener('DOMContentLoaded', function() {
    var dropImageAtCursor = document.querySelectorAll('.dropImageAtCursor-js');

    if (dropImageAtCursor.length === 0) {
        return false;
    }

    [].forEach.call(dropImageAtCursor, function(el) {
        el.addEventListener('click', function() {
            var redactorId = el.getAttribute('data-redactor-id'),
                img = el.parentNode.querySelector('img'),
                imgHtml = document.createElement('img');

            imgHtml.src = img.getAttribute('src');
            imgHtml.alt = img.alt;

            if (img) {
                $('#' + redactorId).redactor('insert.html', imgHtml.outerHTML);
            }

            return false;
        })
    })
});