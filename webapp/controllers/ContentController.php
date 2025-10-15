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


use fractalCms\behaviors\Seo;
use fractalCms\controllers\CmsController;
use fractalCms\helpers\Cms;
use fractalCms\models\Content;
use fractalCms\models\Item;
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
            $content = $this->getContent();
            $itemEntete = $content->getItems()->andWhere(['configItemId' => Cms::getParameter('ITEM', 'ENTETE')])->one();
            $itemsQuery = $content->getItems()->andWhere([
                'not', ['configItemId' => [
                    Cms::getParameter('ITEM', 'ENTETE'),
                    ]]]);
            $sections = static::buildSections($itemsQuery);
            return $this->render('index',
                [
                    'content' => $content,
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
                if ($item->configItemId == Cms::getParameter('ITEM', 'TITRE')) {
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

                    if ($item->configItemId == Cms::getParameter('ITEM', 'IMAGE_HTML')) {

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

