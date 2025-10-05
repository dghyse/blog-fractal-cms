<?php
/**
 * BlogController.php
 *
 * PHP Version 8.2+
 *
 * @author David Ghyse <david.ghysefree.fr>
 * @version XXX
 * @package app\console\controllers
 */
namespace console\controllers;

use yii\console\Controller;
use Exception;
use Yii;

class BlogController extends Controller
{

    protected $dataPath = '@data/blog';

    public function actionCreate()
    {
        try {

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    protected function addConfigType($name, $value) : int
    {
        try {

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    protected function addItemConfigType($name, $value) : int
    {
        try {

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    protected function addParameter($main, $name, $value)
    {
        try {

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    protected function addContents() : int
    {
        try {

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    protected function addItems() : int
    {
        try {

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    protected function addMenu() : int
    {
        try {

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    protected function addMenuItem() : int
    {
        try {

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

}
