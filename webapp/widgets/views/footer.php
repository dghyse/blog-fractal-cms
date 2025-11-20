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
use fractalCms\content\helpers\Html;
use fractalCms\content\helpers\Cms;
use yii\helpers\Url;
use webapp\assets\StaticAsset;
$baseUrl = StaticAsset::register($this)->baseUrl;

$logo = $baseUrl.'/img/logo.webp';
?>
<footer class="shadow-inner bg-[var(--card)] mt-10" role="contentinfo">
    <div class="max-w-6xl mx-auto px-4 py-6 flex flex-col md:flex-row items-center justify-between">
        <div class="flex items-center space-x-2 mt-2">
            <?php
            echo Html::img($logo,
                [
                    'class' => 'w-8 h-8 rounded-full',
                    'width' => 64,
                    'alt' => ''
                ]
            );

            ?>
            <p><?php echo $siteName;?></p>
        </div>
        <nav aria-label="Liens secondaires"  class="">
            <ul class="flex flex-wrap justify-center md:justify-end gap-2">
                <?php
                foreach ($menu as $itemMenu) {
                    echo Html::tag('li', Html::a(Cms::insertIndivisibleSpace($itemMenu['name']), Url::toRoute($itemMenu['route']), ['class' => 'hover:text-blue-400']));
                }
                ?>
            </ul>
        </nav>
    </div>
</footer>







