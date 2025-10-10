<?php
/**
 * entete.php
 *
 * PHP Version 8.2+
 *
 * @version XXX
 * @package webapp\views\layouts
 *
 * @var $this yii\web\View
 * @var $model \fractalCms\models\Item
 * @var $content \fractalCms\models\Content
 */

use fractalCms\helpers\Html;
use yii\helpers\ArrayHelper;
use fractalCms\helpers\Cms;
?>
<?php
//for each attribute
foreach ($model->configItem->configArray as $attribute => $data):?>
    <div class="col form-group p-0 mt-1">
        <?php
        $title = ($data['title']) ?? '';
        $description = ($data['description']) ?? '';
        $options = ($data['options']) ?? null;
        $accept = ($data['accept']) ?? null;
        switch ($data['type']) {
            case Html::CONFIG_TYPE_STRING:
                echo Html::activeLabel($content, 'items['.$model->id.']['.$attribute.']', ['label' => $title, 'class' => 'form-label']);
                echo Html::activeTextInput($content, 'items['.$model->id.']['.$attribute.']', [
                    'placeholder' => $title, 'class' => 'form-control',
                    'value' => $model->$attribute]);
                break;
            case Html::CONFIG_TYPE_FILE:
            case Html::CONFIG_TYPE_FILES:
                echo Html::tag('cms-file-upload', '', [
                    'title.bind' => '\''.$title.'\'',
                    'name' => Html::getInputName($content, 'items['.$model->id.']['.$attribute.']'),
                    'value' => $model->$attribute,
                    'upload-file-text' => 'Ajouter une fichier',
                    'file-type' => $accept
                ]);
                break;
            case Html::CONFIG_TYPE_WYSIWYG:
                echo Html::activeLabel($content, 'items['.$model->id.']['.$attribute.']', ['label' => $title, 'class' => 'form-label']);
                echo Html::activeHiddenInput($content, 'items['.$model->id.']['.$attribute.']', ['value' => $model->$attribute, 'class' => 'wysiwygInput']);
                $inputNameId = Html::getInputId($content, 'items['.$model->id.']['.$attribute.']');
                echo Html::tag('div', '',
                    [
                        'cms-wysiwyg-editor' => 'input-id.bind:\''.$inputNameId.'\'',
                    ]);
                break;
        }
        ?>
    </div>
    <?php if (empty($description) === false):?>
        <div class="col p-0">
            <p class="fw-lighter fst-italic">
                <?php echo $description;?>
            </p>
        </div>
    <?php endif;?>
<?php endforeach;?>
