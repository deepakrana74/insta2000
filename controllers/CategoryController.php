<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\SubCategories;
use app\models\MainCategories;


class CategoryController extends Controller{

    public $enableCsrfValidation = false; //disable csrf 

    //this function gets all the categories and its search tags
    public function actionIndex(){

        $main_category_model = new MainCategories();
        $sub_category_model = new SubCategories();

        //get all the main categories here
        $main_categories = $main_category_model->getParentCategories();

        foreach($main_categories as $main_category){ //go thorough each main_category

            //get all the sub categories here
            $sub_categories = $sub_category_model->getSubCategories($main_category['id']);
            //print_r($sub_categories);
            $sub_categories_name =[];
            foreach($sub_categories as $sub_category_name){ // go through each sub-cat and make array
             $sub_categories_name[] = $sub_category_name['sub_category_name'];
            }
            
            //ready json_response here
            $asd['categories'][] = array(
                'name' => $main_category['main_category_name'],
                'hashtag' => array_slice($sub_categories_name,0,30) 
            );
        }
        //end foreach

        $response = array( // make the response ready
            'status'=>true,
            'data'=> $asd
        );

        // return the response here
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;

    }
}