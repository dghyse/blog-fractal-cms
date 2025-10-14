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
$html = $item?->html;
$image = $item?->image;
$target = $item?->target;
$url = $item?->url;
$html = \fractalCms\helpers\Cms::cleanHtml($html);
$direction = $item?->choix;
$route = null;
if (empty($target) === false) {
    $route = \yii\helpers\Url::toRoute($target);
} elseif (empty($url) === false) {
    $route = $url;
}
?>
<?php
$classes = 'w-[300px] object-cover';
$imageHtml = null;

if (empty($image) === false) {
    if ($direction == 'top')  {
        $classes = 'w-full object-cover';
    }
    $imageHtml =  Html::img($image, [
        'width' => 300,
        'height' => 300,
        'alt' => '',
        'class' => $classes
    ]);
}

if (($direction == 'top' || $direction == 'left') && $imageHtml !== null) {
    echo $imageHtml;
}
?>

<div class="p-6 flex-1">
    <div class="wysiwyg">
        <?php echo $html;?>
    </div>
    <?php
        if ($route !== null) {
            echo Html::a('Voir plus', $route, ['class' => 'btn-link']);
        }
    ?>
</div>
<?php
if ($direction == 'right' && $imageHtml !== null) {
    echo $imageHtml;
}
?>



