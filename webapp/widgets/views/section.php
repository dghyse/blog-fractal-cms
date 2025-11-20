<?php
/**
 * section.php
 *
 * PHP Version 8.2+
 *
 * @version XXX
 * @package webapp\views\layouts
 *
 * @var $this yii\web\View
 * @var array $section
 * @var \fractalCms\content\models\Content $element
 * @var int $index
 *
 */

use fractalCms\content\helpers\Html;

$sectionId = 'home-section-title-'.($index+1);
$hasImg = (isset($section['hasImg']) === true) ? $section['hasImg'] : false;
$direction = (isset($section['direction']) === true) ? $section['direction'] : 'right';
?>
<?php
    $sectionClass = [];
    $sectionClass[] = 'max-w-6xl mx-auto py-3 card rounded-xl shadow-lg overflow-hidden';

    echo Html::beginTag('section', ['class' => implode(' ', $sectionClass), 'aria-describedby' => $sectionId]);
?>
    <?php
    echo \webapp\widgets\Item::widget(['item' => $section['title'], 'element' => $element, 'id' => $sectionId]);
    ?>
    <div class="flex flex-col md:flex-row items-center md:items-start gap-12">
         <?php
            foreach ($section['items'] as $item) {
                echo \webapp\widgets\Item::widget(['item' => $item, 'element' => $element]);
            }
        ?>
    </div>
<?php
echo Html::endTag('section');
?>

