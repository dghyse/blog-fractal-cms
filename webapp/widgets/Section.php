<?php

namespace webapp\widgets;

use fractalCms\models\Content;
use yii\base\Widget;
use Yii;
use Exception;

/**
 * Class Section
 * Widget who generated html for a section
 *
 */
class Section extends Widget
{

    public array $section;
    public Content $element;
    public int $index;


    /**
     * {@inheritDoc}
     */
    public function run()
    {
        try {
            Yii::debug('Trace: '.__METHOD__, __METHOD__);
            $content = null;
            if ($this->element !== null) {
                $content = $this->render('section',
                [
                    'section' => $this->section,
                    'element' => $this->element,
                    'index' => $this->index
                ]
                );
            }
            return $content;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
        }
    }
}
