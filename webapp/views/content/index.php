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
 * @var $content \fractalCms\models\Content
 * @var $entete \fractalCms\models\Item
 * @var $itemsQuery \yii\db\ActiveQuery
 *
 */
use fractalCms\helpers\Html;
use webapp\widgets\Header;
use webapp\widgets\Footer;
use webapp\widgets\Breadcrumb;

$title = ($entete instanceof \fractalCms\models\Item) ? $entete->title : $content->name;
$subtitle = ($entete instanceof \fractalCms\models\Item) ? $entete->subtitle : null;
$banner = ($entete instanceof \fractalCms\models\Item) ? $entete->banner : null;
$description = ($entete instanceof \fractalCms\models\Item) ? $entete->description : null;
$this->title = trim(($content?->seo?->title) ?? $title);

?>
<?php
echo Header::widget([]);
?>
<main id="main" class="max-w-6xl mx-auto py-10 px-4 space-y-12" role="main"  tabindex="-1" portfolio-focus="main">
    <?php
    //echo Breadcrumb::widget(['content' => $content]);
    ?>
    <?php
    $option['class'] = 'relative w-full h-[300px] bg-cover bg-center flex items-center justify-center';
    if ($banner !== null) {
        $option['style'] = 'background-image: url(\''.Html::getImgCache($banner, ['width' => 1200, 'height' => 300]).'\')';
    }
        echo Html::beginTag('section', $option);
    ?>
        <div class="bg-black/50 p-6 rounded-lg text-center max-w-3xl">
            <h1 class="text-4xl font-extrabold"><?php echo $title;?></h1>
            <?php if (empty($subtitle) === false):?>
            <p class="text-lg text-gray-300 mt-2"><?php echo $subtitle;?></p>
            <?php endif;?>
            <div class="text-sm text-gray-400 mt-3">
                <?php echo $description;?>
            </div>
        </div>
    <?php echo Html::endTag('section');?>
    <!-- Hero avec image -->

    <?php
    foreach ($itemsQuery->each() as $item) {
        echo \webapp\widgets\Item::widget(
            [
                'item' => $item,
                'element' => $content,
            ]
        );
    }

    ?>

</main>
<?php
echo Footer::widget([]);
?>







