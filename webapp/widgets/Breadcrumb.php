<?php

namespace webapp\widgets;

use fractalCms\helpers\Cms;
use fractalCms\models\Content;
use webapp\behaviors\JsonLd;
use webapp\helpers\MenuBuilder;
use yii\base\Widget;
use Yii;
use Exception;
use const Grpc\CALL_ERROR_NOT_ON_CLIENT;

class Breadcrumb extends Widget
{

    /**
     * @var Content $content
     */
    public $content;


    /**
     * {@inheritDoc}
     */
    public function run()
    {
        try {
            Yii::debug('Trace: '.__METHOD__, __METHOD__);
            $breadcumb = $this->buildBreadcrumb();
            return $this->render('breadcrumb', [
                'breadcumb' => $breadcumb
            ]);
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
        }
    }

    public function buildBreadcrumb()
    {
        try {
            Yii::debug('Trace: '.__METHOD__, __METHOD__);
            $breadcumb = [];
            $query = $this->content->getParents();

            if ($this->content->type === Content::TYPE_ARTICLE) {
                $query->andWhere(['type' => Content::TYPE_SECTION]);
            }
            /** @var Content $content */
            foreach ($query->each() as $content) {
                if ($content->pathKey !== '1') {
                    $entete = $content->getItemByConfigId(Cms::getParameter('ITEM', 'ENTETE'));
                    $name = ($entete instanceof \fractalCms\models\Item) ? $entete->title : $content->name;
                } else {
                    $name = 'Accueil';
                }
                $breadcumb[] = [
                    'name' => $name,
                    'route' => $content->getRoute()
                ];
            }
            if (count($breadcumb) > 0) {
                $enteteContent = $this->content->getItemByConfigId(Cms::getParameter('ITEM', 'ENTETE'));
                $name = ($enteteContent !== null) ? $enteteContent->title : $this->content->name;
                $breadcumb[] = [
                    'name' => $name,
                    'route' => null,
                ];
            }
            return $breadcumb;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
        }
    }
}
