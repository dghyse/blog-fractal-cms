<?php
/**
 * breadcrumb.php
 *
 * PHP Version 8.2+
 *
 * @version XXX
 * @package webapp\views\layouts
 *
 * @var $this yii\web\View
 * @var array $breadcumb
 *
 */
use fractalCms\helpers\Html;
use yii\helpers\Url;
use webapp\assets\StaticAsset;
$baseUrl = StaticAsset::register($this)->baseUrl;
?>
<?php if (count($breadcumb) > 0 ) :?>
<nav class="container mx-auto px-6 py-4 text-sm text-gray-700" aria-label="Fil d’Ariane">
    <ol class="flex flex-wrap items-center gap-2">
        <?php
            foreach ($breadcumb as $index => $item) {
                $classLi = [];
                if(isset($item['route']) === true) {
                    $html =  Html::a($item['name'], Url::toRoute($item['route']), ['class' =>' hover:text-blue-600']);
                } else {
                    $html =  $item['name'];
                    $classLi[] = 'text-gray-900 font-semibold';
                }
                echo Html::beginTag('li', ['class' => implode(' ', $classLi)]);
                echo $html;
                echo Html::endTag('li');
                if($index < count($breadcumb) - 1) {
                    echo Html::tag('li', '/', ['class' => 'text-gray-400']);
                }

            }
        ?>
    </ol>
</nav>
<?php endif;?>

