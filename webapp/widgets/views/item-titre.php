<?php
/**
 * main.php
 *
 * PHP Version 8.2+
 *
 * @version XXX
 * @package webapp\views\layouts
 *
 * @var $this yii\web\View
 * @var \fractalCms\models\Item $item
 *
 */
use fractalCms\helpers\Html;
$icon = $item?->icon;
$img = null;
?>
<?php
$classes = [];
$classes[] = 'text-2xl font-bold mb-4';
if (empty($icon) === false) {
    $img = Html::img($icon, ['width' => 64, 'height' => 64, 'class' => 'h-8 w-8', 'alt' => '']);
}

echo Html::beginTag('h2', ['class' => implode('', $classes), 'tabindex' => '-1']);
if ($img !== null) {
    echo $img;
}
echo $item->title;

echo Html::endTag('h2');
?>


