<?php
declare(strict_types=1);

require_once 'functions.php';

define('CROP_SIZE', 200);

$params = json_decode(file_get_contents('php://input'), true);

/**
 * 分割したBase64を返却する
 *
 * @param string $fileName
 * @return string
 */
function getBase64Image(string $fileName): string
{
    if (!file_exists($fileName)) {
        return '';
    }
    [0 => $width, 1 => $height, 2 => $imageType, 'mime' => $mime] = getimagesize($fileName);
    if ($imageType === IMAGETYPE_PNG) {
        $im = imagecreatefrompng($fileName);
    } elseif ($imageType === IMAGETYPE_JPEG) {
        $im = imagecreatefromjpeg($fileName);
    } else {
        return '';
    }

    $croppedArray = [];
    for ($i = 0; $i < $height; $i += CROP_SIZE) {
        $croppedChildren = [];
        for ($j = 0; $j < $width; $j += CROP_SIZE) {
            $cropped = imagecrop($im, [
                'x' => $j,
                'y' => $i,
                'width' => $j + CROP_SIZE > $width ? $width - $j : CROP_SIZE,
                'height' => $i + CROP_SIZE > $height ? $height - $i : CROP_SIZE
            ]);
            if ($cropped === false) {
                imagedestroy($im);
                return '';
            }
            imagealphablending($cropped, false);
            imagesavealpha($cropped, true);

            ob_start();
            imagepng($cropped);
            $croppedChildren[] = sprintf('data:%s;base64, %s', $mime, base64_encode(ob_get_clean()));
            imagedestroy($cropped);
        }
        $croppedArray[] = $croppedChildren;
    }
    imagedestroy($im);
    return json_encode($croppedArray);
}

initSession();
$imgPath = getSession('img' . $params['id']);
if ($imgPath === null || !$imgPath) {
    exit();
}

header('Content-type: application/json');
echo getBase64Image((string)$imgPath);
