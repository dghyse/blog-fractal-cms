<?php
/**
 * ContentController.php
 *
 * PHP Version 8.2+
 *
 * @author David Ghyse <david.ghysefree.fr>
 * @version XXX
 * @package app\controllers
 */

namespace webapp\controllers;


use fractalCms\content\models\Seo;
use fractalCms\content\controllers\CmsController;
use fractalCms\content\models\Item;
use fractalCms\core\models\Parameter;
use webapp\behaviors\JsonLd;
use Yii;
use Exception;
use yii\db\ActiveQuery;

/**
 * ContentController class
 *
 * @author David Ghyse <dghyse@redcat.fr>
 * @version XXX
 * @package webapp\controllers
 * @since XXX
 */
class ContentController extends CmsController
{


    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['seo'] = [
            'class' => Seo::class
        ];
        $behaviors['jsonLd'] = [
            'class' => JsonLd::class
        ];
        return $behaviors;
    }

    /**
     * @return \yii\web\Response|string
     * @since XXX
     */
    public function actionIndex()
    {
        try {
            Yii::debug('Trace :'.__METHOD__, __METHOD__);
            $target = $this->getTarget();
            $itemEntete = $target->getItems()->andWhere(['configItemId' => Parameter::getParameter('ITEM', 'ENTETE')])->one();
            $itemsQuery = $target->getItems()->andWhere([
                'not', ['configItemId' => [
                    Parameter::getParameter('ITEM', 'ENTETE'),
                    ]]]);
            $sections = static::buildSections($itemsQuery);
            return $this->render('index',
                [
                    'target' => $target,
                    'entete' => $itemEntete,
                    'sections' => $sections
                    ]);
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }


    protected static function buildSections(ActiveQuery $itemsQuery)
    {
        try {
            Yii::debug('Trace :'.__METHOD__, __METHOD__);
            $sections = [];
            $section = null;
            /** @var Item $item */
            foreach ($itemsQuery->each() as $item) {
                if ($item->configItemId == Parameter::getParameter('ITEM', 'TITRE')) {
                    if ($section !== null) {
                        $sections[] = $section;
                    }
                    $section = [
                        'title' => $item,
                        'items' => []
                    ];
                } else {
                    if (is_array($section['items']) === false) {
                        $section['items'] = [];
                    }

                    if ($item->configItemId == Parameter::getParameter('ITEM', 'IMAGE_HTML')) {

                        $section['type'] = 'IMAGE_HTML';
                        $section['hasImg'] = empty($item?->image);
                        $section['direction'] = $item?->choix;
                    }
                    $section['items'][] = $item;
                }
            }
            if ($section !== null) {
                $sections[] = $section;
            }

            return $sections;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }
}

