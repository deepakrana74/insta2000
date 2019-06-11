<?php

    namespace app\controllers;

    // use yii\rest\ActiveController;
    use app\models\SubCategories;
    use app\models\MainCategories;
    use yii\web\Response;
    use yii\web\Controller;
    use Yii;

    class SearchController extends Controller {

        public $enableCsrfValidation = false; //disable csrf 

        public function actionIndex1() {
            $sub_category_model = new SubCategories; //call the sub_category model
            $keyword = trim(Yii::$app->request->post('keyword'));//get the keyword here
            $limit = Yii::$app->request->post('limit'); //set a limit here
        
         $words = explode(' ',$keyword);
         
         $asd = array();
         foreach($words as $key => $value){
          $url = 'https://query.displaypurposes.com/tag/'.$words[$key];     
          $data = file_get_contents($url);
          $alldatas  = json_decode($data);
        
          $alldata = array_slice($alldatas->results,0,$limit);
          $result = array();
          foreach($alldata as $sad){
            if (!preg_match('/[^A-Za-z0-9]/', $sad->tag)) // '/[^a-z\d]/i' should also work.
            {
              $result[] = '#'.$sad->tag;      
            } 
          }
          //print_r($result); die;
          $asd['categories'][] = array(
            'name' => $value,
            'hashtag' => $result   
            );

            $k = array_rand($result,$limit);
            
            $relevent = array(); 
            for($i=0;$i<count($k);$i++){
               $relevent[] = $result[$k[$i]];
            }
               
         }
         $asd['relevent_tags'] = $relevent;
         
         
         if($result){
           $response = array(
           'status' => true,
           'data' => $asd    
           );
         } else {
           $response = array(
           'status' => false,
           'message' => 'No tags found'     
           );  
         }
         Yii::$app->response->format = Response::FORMAT_JSON;
         return $response;
        }




        // this function deals with single keyword
        public function actionIndex2() {
            
            $sub_category_model = new SubCategories; //call the sub_category model
            
            $keyword = trim(Yii::$app->request->post('keyword'));//get the keyword here
            $limit = Yii::$app->request->post('limit'); //set a limit here
            // echo json_encode(array('status'=>false,'message'=>$keyword));
            // die;
            //if keyword is null, return user from here
            if($keyword == null){
                $response = array(
                    'status' => false,
                    'message' => 'Please Enter A Keyword'     
                    );  
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
            }
            
            //here, check if the keyword is consisted of multiple words or not?
            $break_keyword = explode(' ',$keyword);

            //count the number of keywords
            $break_keyword_length = sizeof($break_keyword);
            
            //first of all, check here if the same exact keyword is present in the database
            $hash_tags = $sub_category_model->hashTags($keyword);
              
            //case 1
            
            if(sizeof($hash_tags) !== 0 && $break_keyword_length === 1){ //if result found and keyword length is =1

                //explode all the hash_tags and store in the container variable hashTag
                $hash_tag_container = explode(',',$hash_tags['hash_tags']);
                      
                if(sizeof($hash_tag_container)!== 30){ // if hashtags are not equal to 30
   
                    //get the group of the particular keyword from the database
                    $search_keyword_group = $sub_category_model->getKeywordGroup($keyword);
                    // print_r($search_keyword_group);   
                    // die;
                    //run the query to get the keyword from the same group
                    $get_hashtags_from_same_group_keywords = $sub_category_model->getGroupHashtags($search_keyword_group->sub_category_group, $keyword);

                    // print_r($get_hashtags_from_same_group_keywords);   
                    // die;

                    $i=0; //initialize i
                    $final_hash_tags = []; //declare an empty array

                    //go through each keyword's hashtag and make them join together collectively
                    foreach($get_hashtags_from_same_group_keywords as $pre_final_hash_tags){
                        
                        //store the hashtags in this array
                        $final_hash_tags[] = explode(',',$pre_final_hash_tags['hash_tags']);
                        $i++; 
                    } 
                    
                    //collect all hashtags in a single array using "call_user_func_array"
                    if($final_hash_tags){
                      $collective = call_user_func_array('array_merge', $final_hash_tags);  
                  } else {
                      $collective = [];
                  }
                    

                    //get the final hashtags and store
                    $final_hash_tag_array = array_merge($hash_tag_container,$collective); 

                    //here get and store the desired hash_tags as per the limit
                    $final_limited_hash_tags = array_slice($final_hash_tag_array,0,$limit); 

                    //return to the json_response function
                    return $this->actionReturnResponse($keyword, $limit, $final_limited_hash_tags);
                }

            }

            //case 2

            elseif(sizeof($hash_tags) !== 0 && $break_keyword_length > 1){ //if result found & keyword length is >1
               
                $case = 2; //case no.2
                return $this->actionMultipleKeywords($keyword, $limit, $case);
                
            }

            //case 3
        
            elseif(sizeof($hash_tags) == 0 && $break_keyword_length === 1){ //if result !found & keyword length is =1
                
                //do anything but I want results... just run Like condition
                $like_hash_tags = $sub_category_model->likeHashTags($keyword);

                if($like_hash_tags == null){
                    $response = array(
                        'status' => false,
                        'message' => 'No result found. Please try another keyword'     
                        );  
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return $response;
                }

                //explode all the hash_tags and store in the container variable hashTag
                $hash_tag_container = explode(',',$like_hash_tags['hash_tags']); 

                if(sizeof($hash_tag_container)!== 30){ // if hashtags are not equal to 30

                    //get the group of the particular keyword from the database
                    $search_keyword_group = $sub_category_model->getKeywordGroup($like_hash_tags['sub_category_name']);


                    //run the query to get the keyword from the same group
                    $get_hashtags_from_same_group_keywords = $sub_category_model->getGroupHashtags($search_keyword_group->sub_category_group, $like_hash_tags['sub_category_name']);

                    $i=0; //initialize i
                    $final_hash_tags = []; //declare an empty array

                    //go through each keyword's hashtag and make them join together collectively
                    foreach($get_hashtags_from_same_group_keywords as $pre_final_hash_tags){
                        
                        //store the hashtags in this array
                        $final_hash_tags[] = explode(',',$pre_final_hash_tags['hash_tags']);
                        $i++; 
                    }    
                    
                    //collect all hashtags in a single array using "call_user_func_array"
                    if($final_hash_tags){
                      $collective = call_user_func_array('array_merge', $final_hash_tags);
                    } else {
                      $collective = [];
                    }
                    

                    //get the final hashtags and store
                    $final_hash_tag_array = array_merge($hash_tag_container,$collective); 

                    //here get and store the desired hash_tags as per the limit
                    $final_limited_hash_tags = array_slice($final_hash_tag_array,0,$limit); 

                    //return to the json_response function
                    return $this->actionReturnResponse($keyword, $limit, $final_limited_hash_tags);
                }




                if($hash_tags == null){ // if still results are empty... I can't do anything then..

                    $response = array(
                        'status' => false,
                        'message' => 'No result found. Please try another keyword'     
                        );  
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return $response;
                }
                else{ //return to the return response function
                    return $this->actionReturnResponse($keyword, $limit, $hash_tags);
                }
            }

            //case 4
    
            elseif(sizeof($hash_tags) == 0 && $break_keyword_length > 1){ // if result !found & keyword length is >1
                
                $case = 4; //declare the case here
                return $this->actionMultipleKeywords($keyword, $limit, $case);
            }
        
        }

        //***************************************************************************************************** *///

        //this function deals with multiple keywords search
        public function actionMultipleKeywords($keyword, $limit, $case) {
            
            $sub_category_model = new SubCategories;

            //here, break the string in multiple keywords?
            $break_keyword = explode(' ',$keyword);
            
            //count the number of keywords
            $break_keyword_length = sizeof($break_keyword); 

            $hash_tags = []; //get an empty array

            $newarr = array();
            $asd = [];

            $initiator=1; $index=0;
            for($initiator; $initiator<=$break_keyword_length; $initiator++){

                if($case == 2){ // if the request is from case 2
                    
                    $hash_tags[] = $sub_category_model->hashTags($keyword); //query to get the result

                    if($hash_tags[$index] !== null){ // if results are found
                        
                        $hash_tag_container = explode(',',$hash_tags[$index]['hash_tags']); //just store the hashtags

                        $break_the_loop = true; //and break the loop

                    }

                }
                elseif($case = 4){ // if the request is from case 4
                
                    $hash_tags[] = $sub_category_model->likeHashTags($break_keyword[$index]);

                    if($hash_tags[$index]==null){ // if still results not found,
                        $response = array(
                            'status' => false,
                            'message' => 'No result found. Please try another keyword'     
                            );  
                            Yii::$app->response->format = Response::FORMAT_JSON;
                            return $response;
                    }
    
                    $hash_tag_container = explode(',',$hash_tags[$index]['hash_tags']); //store all keywords in container

                }
                
                if(sizeof($hash_tag_container) !== 30) { // if hashtags are not equal to 30

                    //get the group of the particular keyword from the database
                    $search_keyword_group = $sub_category_model->getKeywordGroup($hash_tags[$index]['sub_category_name']);
                    
                    //run the query to get the keyword from the same group
                    $get_hashtags_from_same_group_keywords = $sub_category_model->getGroupHashtags($search_keyword_group->sub_category_group, $hash_tags[$index]['sub_category_name']);
                    // print_r($search_keyword_group);
                    // die;
                     //go through each keyword's hashtag and make them join together collectively
                     $final_hash_tags = [];
                     foreach($get_hashtags_from_same_group_keywords as $pre_final_hash_tags){
                        
                        //store the hashtags in this array  
                        $final_hash_tags[] = explode(',',$pre_final_hash_tags['hash_tags']); 
                     }
                    

                    //collect all hashtags in a single array using "call_user_func_array"
                    if($final_hash_tags){
                      $collective = call_user_func_array('array_merge', $final_hash_tags);   
                    } else {
                      $collective = [];   
                    }
                    

                    //get the final hashtags and store
                    $final_hash_tag_array = array_merge($hash_tag_container,$collective); 
                        
                    //here get and store the desired hash_tags as per the limit
                    $final_limited_hash_tags = array_slice($final_hash_tag_array,0,$limit); 
                        
                    //return to the json_response function
                    $asd['categories'][] = array(
                        'name' => $break_keyword[$index],
                        'hashtag' => $final_limited_hash_tags 
                        );
                        
                        $k = array_rand($final_limited_hash_tags,$limit);
                        
                        $relevent = array();
                        for($i=0;$i<count($k);$i++){
                          if(count($k)>1){
                            $asds[] = trim($final_limited_hash_tags[$k[$i]]);
                          } else {
                            $asds[] = trim($final_limited_hash_tags[$k]);
                          }  
                        }
                        $asd['relevent_tags'] = array_slice($asds,0,$limit);  
                    if($final_limited_hash_tags){
                       $response = array(
                       'status' => true,
                       'data' => $asd    
                       );
                    } else {
                       $response = array(
                       'status' => false,
                       'message' => 'No tags found'     
                       );  
                    }   
                    if(isset($break_the_loop)){ // if loop breaking is 'on'
                        $case = 4;
                        $revisit = true;
                        return $this->actionMultipleKeywords($keyword, $limit, $case);
                    }   
                    $index++; // increase the value of index by 1
                }
            }

            if($asd){
                $response = array(
                    'status' => true,
                    'data' => $asd    
                    );  
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Nothing'    
                    ); 
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;

        }
// ******************************************************************************************************************//

        //response of the result function
        public function actionReturnResponse($keyword, $limit, $final_limited_hash_tags){

            $asd['categories'][] = array(
                    'name' => $keyword,
                    'hashtag' => $final_limited_hash_tags 
                    );
                    if(count($final_limited_hash_tags)>$limit){
                     $k = array_rand($final_limited_hash_tags,$limit);  
                    } else {
                     $k = array_rand($final_limited_hash_tags,count($final_limited_hash_tags)); 
                    }
                    
                    // print_r($k);
                    // die;
                    $relevent = array(); 
                    for($i=0;$i<count($k);$i++){
                       if(count($k)>1){
                         $relevent[] = $final_limited_hash_tags[$k[$i]];
                       } else {
                         $relevent[] = $final_limited_hash_tags[$k];
                       }
                       
                    }
                       
                 $asd['relevent_tags'] = $relevent;
                 
                 
                 if($final_limited_hash_tags){
                   $response = array(
                   'status' => true,
                   'data' => $asd    
                   );
                 } else {
                   $response = array(
                   'status' => false,
                   'message' => 'No tags found'     
                   );  
                 }
                 Yii::$app->response->format = Response::FORMAT_JSON;
                 return $response;
        }


        public function actionIndex_old() {
         $sub_category_model = new SubCategories; //call the sub_category model
         $main_category_model = new MainCategories;
         $keyword = trim(Yii::$app->request->post('keyword'));//get the keyword here
         $limit = Yii::$app->request->post('limit'); //set a limit here
         
         if($keyword == null) {
          $response = array(
          'status'=>false,
          'message'=>'Please enter keywords to search'	
          );
          Yii::$app->response->format = Response::FORMAT_JSON;
          return $response;
         }

         $keywordList = explode(' ',$keyword);
         $keywordcount = sizeof($keywordList);

         if($keywordcount < 2) {
            $hashTags = $sub_category_model->singlekeywordsearch($keyword); 
           // print_r($mixedtags);
           // die;

           if($hashTags) {

           
           $cats = $hashTags['cats'];
           $subtags = array_slice($hashTags['tags'],0,3);

           $maintags = $main_category_model->getmaincategoryTags($cats);
           $mixedforcats = array_merge($subtags,$maintags);
           $mixedtags = array_slice(array_merge($subtags,$maintags),0,$limit);
           	$data = array(	
            'categories' => array(array(
            			  "name" => $keyword,
            			  "hashtag" => array_slice($mixedforcats,0,$limit) 	
            	          )),
            'relevent_tags' => $mixedtags
           	);
           	$response = array(
            'status' => true,
            'data' =>$data 
           	);
           	Yii::$app->response->format = Response::FORMAT_JSON;
           	return $response;
           } else {
           	$response = array(
            'status' => false,
            'message' => 'No hashtags found' 
           	);
           	Yii::$app->response->format = Response::FORMAT_JSON;
           	return $response;
           }
         } else {
          // when more than one words is searched
          $hashTags = $sub_category_model->multiKeywordSearch($keyword,$limit);
          
          if($hashTags){
           $response = array(
	       'status' => true,
	       'data' => $hashTags
	       );
           Yii::$app->response->format = Response::FORMAT_JSON;
	       return $response;
          } else {
           $response = array(
	       'status' => false,
	       'message' => 'No hashtags found'
	       );
           Yii::$app->response->format = Response::FORMAT_JSON;
	       return $response;
          }
          
         }

         
        }

        /***** New Search Function ******/

        public function actionIndex() { 
         $sub_category_model = new SubCategories; //call the sub_category model
         $main_category_model = new MainCategories; //call the main_category model

         $keyword = trim(Yii::$app->request->post('keyword'));//get the keyword here
         $limit = Yii::$app->request->post('limit'); //set a limit here
         //$method = Yii::$app->request->post('method'); //Auto or Manual
         
         if($keyword == null) {
          $response = array(
          'status'=>false,
          'message'=>'Please enter keywords to search'	
          );
          Yii::$app->response->format = Response::FORMAT_JSON;
          return $response;
         }

         $keywords = explode(',',$keyword);
         
         $response = $sub_category_model->hashTagSearch($keywords,$limit);
         // print_r($response);
         // die;
         if($response) {
           $data = array(
                   'status' => true,
                   'data' => $response
            	       );
           Yii::$app->response->format = Response::FORMAT_JSON;
	       return $data;
         } else { 
           $data = array(
           		   'status' => false,
           		   'message' => 'No hash tags found'	
           	       );
           Yii::$app->response->format = Response::FORMAT_JSON;
	       return $data;
         } 
        
        }

    //***********************************************************************************************/



   public function actionMyFunction() { 
         $sub_category_model = new SubCategories; //call the sub_category model
         $main_category_model = new MainCategories; //call the main_category model

         $keyword = trim(Yii::$app->request->post('keyword'));//get the keyword here
         $limit = Yii::$app->request->post('limit'); //set a limit here
         //$method = Yii::$app->request->post('method'); //Auto or Manual
         
         if($keyword == null) {
          $response = array(
          'status'=>false,
          'message'=>'Please enter keywords to search'	
          );
          Yii::$app->response->format = Response::FORMAT_JSON;
          return $response;
         }

         $keywords = explode(',',$keyword);
         
         $response = $sub_category_model->Search($keywords,$limit);
         // print_r($response);
         // die;
         if($response) {
           $data = array(
                   'status' => true,
                   'data' => $response
            	       );
           Yii::$app->response->format = Response::FORMAT_JSON;
	       return $data;
         } else { 
           $data = array(
           		   'status' => false,
           		   'message' => 'No hash tags found'	
           	       );
           Yii::$app->response->format = Response::FORMAT_JSON;
	       return $data;
         } 
        
        }



    }

?>