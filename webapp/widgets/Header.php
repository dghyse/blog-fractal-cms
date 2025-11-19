<?php

namespace webapp\widgets;

use fractalCms\core\models\Parameter;
use yii\base\Widget;
use Yii;
use Exception;
class Header extends Widget
{

    public $element;
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        try {
            Yii::debug('Trace: '.__METHOD__, __METHOD__);
            $siteName = Parameter::getParameter('SITE', 'NAME');
            return $this->render('header', [
                'element' => $this->element,
                'siteName' => $siteName
            ]);
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
        }
    }
}
