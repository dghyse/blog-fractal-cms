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
 * @var string $siteName
 * @var array $menu
 *
 */
use fractalCms\helpers\Html;
use yii\helpers\Url;
use webapp\assets\StaticAsset;
$baseUrl = StaticAsset::register($this)->baseUrl;

$logo = $baseUrl.'/img/logo.webp';
?>
<footer class="shadow-inner bg-[var(--card)] mt-10" role="contentinfo">
    <div class="max-w-6xl mx-auto flex items-center justify-between p-4 text-sm">
        <div class="flex items-center space-x-2">
            <?php
            echo Html::img($logo,
                [
                    'class' => 'w-10 h-10 rounded-full',
                    'width' => 64,
                    'height' => 64,
                    'alt' => ''
                ]
            );

            ?>
            <span><?php echo $siteName;?></span>
        </div>
        <nav aria-label="Liens secondaires" class="space-x-6">
            <?php
                foreach ($menu as $itemMenu) {
                    echo Html::a(\fractalCms\helpers\Cms::insertIndivisibleSpace($itemMenu['name']), Url::toRoute($itemMenu['route']), ['class' => 'hover:text-blue-600']);
                }
            ?>
        </nav>
    </div>
</footer>







