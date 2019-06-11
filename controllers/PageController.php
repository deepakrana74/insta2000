<?php

    namespace app\controllers;

    use Yii;
    use yii\web\Controller;
    use yii\web\Response;
    use app\models\Pages;

    class PageController extends Controller{
        public $enableCsrfValidation = false; //disable csrf

        public function actionIndex(){

            //call the model here
            $page_model = new Pages();

            //get the page Id here
            $page_id = Yii::$app->request->post('page_id');

            //query to get the result
            $page_content = $page_model->getPage($page_id);

            $asd = array(
                'status'=>true,
                'name'=>$page_content['name'],
                'data'=>$page_content['content']
            );

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $asd;
        }
    }
