<?

/* @var $this yii\web\View */
/* @var $from string Секектор откуда выбирать значение */
/* @var $to string Селектор куда вставить значение */
/* @var $toLower  */
?>
<script>
/**
* Транслитерация из названия в слаг
*/
document.addEventListener('DOMContentLoaded', function () {
    var title = document.querySelector('<?= $from ?>'),
        slug = document.querySelector('<?= $to ?>'),
        enabled = document.querySelector('.translitEnabled-js'),
        toLower = <?= $toLower ?>;

    var transliterate = function (input) {
        var rusChars = ['А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П',
                'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ч', 'Ц', 'Ш', 'Щ', 'Э', 'Ю', 'Я', 'Ы', 'Ъ', 'Ь', 'а', 'б', 'в', 'г',
                'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ч',
                'ц', 'ш', 'щ', 'э', 'ю', '\я', 'ы', 'ъ', 'ь', '\u0020', '!', '@', '#', '$', '%', '^', '&', '*',
                '(', ')', '+', '=', '\`', '~', '.', ',', '', '\'', '/', '\u005C', ':', ';', '?', '<', '>', '"',
                '{', '}', '[', ']', '\u0022', '«', '»', '”', '№', '\u2013', '\u2014', '\u2015'],
            transChars = ['A', 'B', 'V', 'G', 'D', 'E', 'Jo', 'Zh', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
                'P', 'R', 'S', 'T', 'U', 'F', 'H', 'Ch', 'C', 'Sh', 'Csh', 'E', 'Ju', 'Ja', 'Y', '', '', 'a', 'b',
                'v', 'g', 'd', 'e', 'jo', 'zh', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u',
                'f', 'h', 'ch', 'c', 'sh', 'csh', 'e', 'ju', 'ja', 'y', '', '', '-', '', '', '', '', '', '', '', '',
                '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
            output = '',
            isRus, character;

        input = input.replace(/^\s+|\s+$/g, ''); // trim
        var strLength = input.length;

        if (strLength === 0) {
            return '';
        }

        for (var i = 0; i <= strLength - 1; i++) {
            character = input.charAt(i);

            isRus = false;
            for (j = 0; j < rusChars.length; j++) {
                if (character == rusChars[j]) {
                    isRus = true;
                    break;
                }
            }
            output += (isRus) ? transChars[j] : character;

            if (toLower) {
                output = output.toLowerCase();
            }
        }

        return output;
    };

    if (title && slug) {
        title.addEventListener('input', function () {
            if (enabled.checked) {
                slug.value = transliterate(title.value);
            }
        })
    }
});
</script>