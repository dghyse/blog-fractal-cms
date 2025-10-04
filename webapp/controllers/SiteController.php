<?php
/**
 * main.php
 *
 * PHP Version 8.2+
 *
 * @author David Ghyse <david.ghysefree.fr>
 * @version XXX
 * @package app\config
 */

namespace webapp\controllers;


use fractalCms\actions\SitemapAction;
use fractalCms\helpers\Cms;
use fractalCms\models\Content;
use webapp\actions\RobotTxtAction;
use webapp\helpers\MenuBuilder;
use Yii;
use Exception;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * SiteController class
 *
 * @author David Ghyse <dghyse@redcat.fr>
 * @version XXX
 * @package webapp\controllers
 * @since XXX
 */
class SiteController extends Controller
{

    public function actions()
    {
        $actions = parent::actions();
        $actions['sitemap'] = [
            'class' => SitemapAction::class
        ];
        $actions['robot-txt'] = [
            'class' => RobotTxtAction::class
        ];
        return $actions;
    }

    /**
     * @return \yii\web\Response|string
     * @since XXX
     */
    public function actionIndex()
    {
        try {
            Yii::debug('Trace :'.__METHOD__, __METHOD__);
            $content = Content::findOne(1);
            if ($content !== null) {
                return $this->redirect(Url::toRoute($content->getRoute()));
            }
            return $this->render('index', []);
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }
}

