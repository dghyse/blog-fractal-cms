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
 * @var $target \fractalCms\models\Content
 * @var $entete \fractalCms\models\Item
 * @var $sections array
 */
use fractalCms\helpers\Html;
use webapp\widgets\Header;
use webapp\widgets\Footer;
use webapp\widgets\Breadcrumb;

$title = ($entete instanceof \fractalCms\models\Item) ? $entete->title : $target->name;
$subtitle = ($entete instanceof \fractalCms\models\Item) ? $entete->subtitle : null;
$banner = ($entete instanceof \fractalCms\models\Item) ? $entete->banner : null;
$description = ($entete instanceof \fractalCms\models\Item) ? $entete->description : null;
$this->title = trim(($target?->seo?->title) ?? $title);

?>
<?php
echo Header::widget([]);
?>
<main id="main" class="space-y-16" role="main"  tabindex="-1" portfolio-focus="main">
    <?php
    echo Breadcrumb::widget(['content' => $target]);
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
            <h2 class="text-2xl mt-2"><?php echo $subtitle;?></h2>
            <?php endif;?>
            <div class="text-sm mt-3 text-gray-200">
                <?php echo $description;?>
            </div>
        </div>
    <?php echo Html::endTag('section');?>
    <!-- Hero avec image -->

    <?php
    foreach ($sections as $index => $section) {
        echo \webapp\widgets\Section::widget(
            [
                'section' => $section,
                'element' => $target,
                'index' => $index
            ]
        );
    }
    ?>

</main>
<?php
echo Footer::widget([]);
?>







