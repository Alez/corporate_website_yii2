// Превью для товара
document.addEventListener('DOMContentLoaded', function() {
    [].forEach.call(document.querySelectorAll('.imageInput-js'), function(el) {
        el.addEventListener('change', function() {
            if (this.files[0].type.match('image')) {
                if (this.files && this.files[0]) {
                    var previewClass = 'imagesPreview-js',
                        galleryList = this.parentNode.querySelector('.' + previewClass);

                    if (!galleryList) {
                        galleryList = document.createElement('div');
                        galleryList.className = 'imagesPreview ' + previewClass;
                        this.parentNode.insertBefore(galleryList, this);
                    }

                    var previewImages = galleryList.querySelectorAll('.' + previewClass);
                    [].forEach.call(previewImages, function(el) {
                        el.parentNode.removeChild(el);
                    });

                    for (var i = 0; i < this.files.length; i++) {
                        var reader = new FileReader();

                        reader.addEventListener('load', function (e) {
                            var newImg = document.createElement('img');

                            newImg.src = e.target.result;
                            newImg.className = previewClass;

                            galleryList.appendChild(newImg);
                        });

                        reader.readAsDataURL(this.files[i]);
                    }
                }
            }
        }, false);
    });
});