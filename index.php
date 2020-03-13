<?php
declare(strict_types=1);

require_once 'functions.php';

initSession();
$imgFiles = glob('img/*.*');
foreach ($imgFiles as $key => $imgFile) {
    saveSession('img' . $key, $imgFile);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>image test</title>
    <script defer
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous"></script>
    <script defer>
    window.addEventListener('load', () => {
        $(() => $('div.image_outer').map(id => {
            fetch('/image.php', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
                },
                body: JSON.stringify({
                    id: id,
                }),
            }).then(response => response.json())
                .then(imageJson => {
                    imageJson.map((tileLine, idx1) => {
                        const $img = $(`div.img${id}`);
                        tileLine.map((tile, idx2) => $img.append(`<img src= "${tile}" alt="${id}${idx1}${idx2}" style="pointer-events: none">`));
                        $img.append(`<br>`);
                    });
                });
        }));
    });
    </script>
</head>
<body>
<?php foreach ($imgFiles as $key => $imgFile): ?>
    <div class="img<?= $key ?> image_outer" data-id="<?= $key ?>" style="display: flex;flex-wrap: wrap;"></div>
<?php endforeach; ?>
</body>
</html>