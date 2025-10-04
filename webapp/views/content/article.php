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
 * @var $header \fractalCms\models\Item
 * @var $footer \fractalCms\models\Item
 * @var $sections array
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
echo Header::widget(['item' => $header]);
?>
<main id="main" role="main" tabindex="-1" portfolio-focus="main">
    <?php
        echo Breadcrumb::widget(['content' => $content]);
    ?>
    <!-- Hero avec image -->
    <section id="home" class="relative text-white">
        <!-- Image de fond -->
        <div class="absolute inset-0 h-72">
            <?php
            if (empty($banner) === false) {
                echo Html::img($banner, [
                    'width' => 1200, 'height' => 300,
                    'alt' => 'Image hero',
                    'class' => 'w-full h-full object-cover'
                ]);
            }
            ?>
            <!-- Overlay -->
            <div class="absolute inset-0 bg-blue-800 opacity-70"></div>
        </div>

        <!-- Contenu -->
        <div class="relative container mx-auto px-6 h-72 flex flex-col justify-center items-center text-center">
            <h1 class="text-3xl md:text-5xl font-extrabold"><?php echo $title;?></h1>
            <div class="mt-2 text-lg text-blue-100">
                <?php echo $description;?>
            </div>
            <div class="mt-4 flex gap-4">
                <?php
                if (empty($ctatitle) === false && empty($target) === false) {
                    echo Html::a(
                        $ctatitle,
                        \yii\helpers\Url::toRoute($target),
                        [
                            'class' => 'bg-white text-blue-700 font-semibold px-6 py-2 rounded-lg shadow hover:bg-gray-100 transition'
                        ]
                    );
                }
                ?>
            </div>
        </div>
    </section>



    <?php
    foreach ($sections as $index => $section) {
        echo \webapp\widgets\Section::widget(
            [
                'section' => $section,
                'element' => $content,
                'index' => $index
            ]
        );
    }
    ?>

</main>
<?php
echo Footer::widget(['item' => $footer]);
?>







