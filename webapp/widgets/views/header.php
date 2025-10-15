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
 * @var \fractalCms\models\Content $element
 * @var string $siteName
 *
 */
use webapp\assets\StaticAsset;
use fractalCms\helpers\Html;
$baseUrl = StaticAsset::register($this)->baseUrl;

$logo = $baseUrl.'/img/logo.webp';

?>
<header class="shadow-md bg-[var(--card)]" role="banner">
    <a href="#main"
       class="sr-only focus:not-sr-only px-4 py-2"
    >
        Aller au contenu principal
    </a>
    <div class="max-w-6xl mx-auto flex items-center justify-between p-4">
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
            <span class="text-xl font-bold"><?php echo $siteName;?></span>
        </div>
        <?php
        echo \webapp\widgets\MenuNav::widget([]);
        ?>
    </div>
</header>

