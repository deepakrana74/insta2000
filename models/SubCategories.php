<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sub_categories".
 *
 * @property int $id
 * @property string $sub_category_name
 * @property string $sub_category_group
 * @property int $main_category_id
 *
 * @property MainCategories $mainCategory
 */
class SubCategories extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sub_categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sub_category_name', 'sub_category_group', 'main_category_id'], 'required'],
            [['main_category_id'], 'integer'],
            [['sub_category_name'], 'string', 'max' => 10],
            [['sub_category_group'], 'string', 'max' => 8],
            [['main_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => MainCategories::className(), 'targetAttribute' => ['main_category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sub_category_name' => 'Sub Category Name',
            'sub_category_group' => 'Sub Category Group',
            'main_category_id' => 'Main Category ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainCategory()
    {
        return $this->hasOne(MainCategories::className(), ['id' => 'main_category_id']);
    }

    /**
     * {@inheritdoc}
     * @return SubCategoriesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SubCategoriesQuery(get_called_class());
    }

    //hashtag finding query is written here
    public function hashTags($keyword){

        $hashTags = SubCategories::find()
                    ->select(['hash_tags', 'sub_category_name'])
                    ->from('sub_categories')
                    ->where(['sub_category_name'=>$keyword])
                    ->asArray()
                    ->one();

        return $hashTags;     
    }

    //********************************************************************************************//
    
    //hashtagn finding using like conditon query 
    public function likeHashTags($keyword){
        $hash_tags = SubCategories::find()
                    ->select(['hash_tags','sub_category_name'])
                    ->from('sub_categories')
                    ->where(['like','sub_category_name', $keyword])
                    ->asArray()
                    ->one(); 

        return $hash_tags;
    }

    //query to find the keyword group
    public function getKeywordGroup($keyword){
        $hash_tags = SubCategories::find()
                    ->select(['sub_category_group'])
                    ->where(['sub_category_name'=> $keyword])
                    ->one(); 

        return $hash_tags;
    }

    
    //hashtag finding query is written here
    public function getGroupHashtags($group, $keyword){

        $hashTags = SubCategories::find()
                    ->select(['hash_tags'])
                    ->where(['sub_category_group'=>$group])
                    ->andWhere(['!=', 'sub_category_name', $keyword])
                    ->asArray()
                    ->all();

        return $hashTags;     
    }

    // all category and hashtag find query
    public function getSubCategories($main_category_id){
        $sub_categories = SubCategories::find()
                    ->select('*')
                    ->where(['main_category_id'=>$main_category_id])
                    ->asArray()
                    ->all();

        return $sub_categories;
    }

    // public function searchKeyword($keyword,$limit){
    //   $keys = explode(' ',$keyword);
    //    $hashTags = array();
    //     for($i=0;$i<count($keys);$i++){
    //         $hashTags[$keys[$i]] = SubCategories::find()
    //         ->select(['hash_tags'])
    //         ->where(['like','sub_category_name', $keys[$i]])
    //         ->asArray()
    //         ->all();   
    //     }
    //    return $hashTags;
    // }

    // public function singlekeywordsearch($keyword) {
    //   $hashTags = SubCategories::find()
    //         ->select('*')
    //         ->where(['like','sub_category_name', $keyword])
    //         ->asArray()
    //         ->all();
      
      
    //   if($hashTags){
    //     $asd = array();
    //     $categories = array();

    //     foreach($hashTags as $hashTag) 
    //     { 
    //      $asd[] = explode(',',$hashTag['hash_tags']);
    //      $categories[] = $hashTag['main_category_id'];
    //     }

    //     $combinedarray = call_user_func_array('array_merge',$asd);
    //     if(count($combinedarray)>30){
    //      $randomElement = array_rand($combinedarray, 30);
    //      for($i=0;$i<count($randomElement);$i++) {
    //       $realtags[] = $combinedarray[$randomElement[$i]];
    //      }  
    //     } else {
    //      $realtags = $combinedarray; 
    //     }
        

        
    //     $data = array(
    //      'tags' => $realtags,
    //      'cats' => $categories  
    //     );
    //     return $data;
    //   } else {
    //     return false;
    //   }        
    // }

    // public function multiKeywordSearch($keyword,$limit) {

    //  // Check all words for exact match
    //  $queryDetails = new \yii\db\Query();
    //  $hashTags = $queryDetails->select('*')
    //     ->from('sub_categories')
    //     ->join('inner join', 'main_categories', 'sub_categories.main_category_id = main_categories.id')    
    //     ->where(['sub_categories.sub_category_name'=> $keyword])
    //     ->all();
           
    //  if($hashTags)
    //  {
    //    $subtags = explode(',',$hashTags[0]['hash_tags']);
    //    $maintags = explode(',',$hashTags[0]['main_category_keywords']);

    //    if(count($subtags)>3) {
    //     $threeSub = array_rand($subtags,3);

    //     //first three hashtags from subcategories
    //     $threesubtags = array();
    //     for($i=0;$i<count($threeSub);$i++) {
    //      $threesubtags[] = $subtags[$i];
    //     }
    //    } else {
    //     $threesubtags = $subtags; 
    //    }

    //    if(count($maintags)>27) {
    //     $twentyseventags = array_rand($maintags,27);

    //     //Remaining 27 hashtags from main categories
    //     $allmaintags = array();
    //     for($j=0;$j<count($twentyseventags);$j++) {
    //      $allmaintags[] = $maintags[$j];
    //     }
    //    } else {
    //     $allmaintags = $maintags; 
    //    }

    //    $combinedarray = array_merge($threesubtags,$allmaintags);
    //    $data =  array(
    //             'categories' =>array(array(
    //                            'name' => $keyword,
    //                            'hashtag' => $combinedarray 
    //                            )),
    //             'relevent_tags' => array_slice($combinedarray,0,$limit)  
    //             );
    //    return $data; 

    //  } else {

    //    // Search by single word
    //    $keywords = explode(' ',$keyword);
    //    $tags = $this->gettagsfrommultipleKeyword($keywords,$limit);
    //    if($tags){

    //    $count = count($tags);
    //    $subtaglimit = round(10/$count);
    //    $maintaglimit = (30-($subtaglimit));

       
    //      $subHashTags = array();
    //      $temp = array();

    //      for($a=0;$a<count($tags);$a++)
    //      {
            
    //       $temp[$a]['keyword'] = $tags[$a]['keyword']; 
    //       $temp[$a]['subtags'] = explode(',',$tags[$a]['tagsdata']['hash_tags']);
    //       $temp[$a]['mainTags'] = explode(',',$tags[$a]['tagsdata']['main_category_keywords']);
          
    //      }

    //      $countcheck = (count($temp));

    //      $check = array();
    //      foreach ($temp as $asd) {
            
    //         $check['suball'][] = array_slice($asd['subtags'],0,(10/$countcheck));
    //         $check['mainall'][] = array_slice($asd['mainTags'],0,20);
              
    //        $data['categories'][] = array(
    //        'name' =>$asd['keyword'],
    //        'hashtag'=> array_merge(array_slice($asd['subtags'],0,$subtaglimit),array_slice($asd['mainTags'],0,$maintaglimit))  
    //        );
            
    //      }

    //      $printsub = call_user_func_array('array_merge', $check['suball']);
    //      $printmain = call_user_func_array('array_merge', $check['mainall']);
  
    //      $randommaintags = array_rand($printmain,20);

    //      $randmain = array();
    //      for($h=0;$h<count($randommaintags);$h++) {
    //       $randmain[] = $printmain[$randommaintags[$h]];
    //      }
         
    //      $data['relevent_tags'] = array_slice(array_merge($printsub,$randmain),0,$limit);
    //      return $data;
 
    //    } else {
    //      return false; 
    //    }
    //  }           
    // }

    // public function gettagsfrommultipleKeyword($keywords,$limit) {  
    //   $hashes = array();
    //   for($i=0;$i<count($keywords);$i++) {
    //     $searchkey = trim($keywords[$i]);
    //     $individualkeycheck = $this->getindividualexacthashtags($searchkey);
    //     if($individualkeycheck) {
    //       $hashes[] = array(
    //                   'keyword'=>$searchkey,
    //                   'tagsdata'=>$individualkeycheck
    //                   );    
    //     }
    //   }
    //   return $hashes;
    // }


    // public function searchtagbyLike($searchkey) {
    //  $queryDetails = new \yii\db\Query();
    //  $hashTags = $queryDetails->select('*')
    //     ->from('sub_categories')
    //     ->join('inner join', 'main_categories', 'sub_categories.main_category_id = main_categories.id')    
    //     ->where(['like','sub_categories.sub_category_name', $searchkey])
    //     ->all();
    //  return $hashTags;       
    // }

    public function hashTagSearch($keywords,$limit) { 
    $HashTags = array();
     $categories = array();  
     for($i=0;$i<count($keywords);$i++) 
     {

       $Result = $this->getindividualexacthashtags($keywords[$i]);
       if($Result) {
        $SearchResult = $Result;
       } else {
        $SearchResult = $this->getindividualexacthashtagslike($keywords[$i]); 
       }
       
       if($SearchResult) 
       { 
           // Separate Sub category Tags and Main category Tags
           //$subCategoryTags = explode(",",$SearchResult['hash_tags']);
           $static = explode(",",$SearchResult['hash_tags']);
           if($i == 0){ $clr ='#000000'; } elseif($i == 1){ $clr = '#0000FF'; } elseif($i == 2) { $clr = '#FF0000'; }
           $prefix = "_".$clr.',';
           $colored = implode("_" . $prefix,$static).$prefix;
           $subCategoryTags = explode(",",$colored);
           //print_r($subCategoryTags);
           
           //$mainCategoryTags = explode(",",$SearchResult['main_category_keywords']);
           $static2 = explode(",",$SearchResult['main_category_keywords']);
           $colored2 = implode("_" . $prefix,$static2).$prefix;
           $mainCategoryTags = explode(",",$colored2);
           
           // Combining both subcatgory and main category tags to provide random result after first 6 hash tags
           $combinedHashTags = array_merge($subCategoryTags,$mainCategoryTags);
           
           // Get random 24 Tags from sub and main category
           $randomindexes = array_rand($combinedHashTags,25);
           $RandomHashTags = array();
           for($k=0;$k<count($randomindexes);$k++) {
             $RandomHashTags[] = $combinedHashTags[$randomindexes[$k]];
           }
           
           // Make array of first static Hashtags from Sub and main category
           $mixedStatic = array($subCategoryTags[0],$subCategoryTags[1],$subCategoryTags[2],$mainCategoryTags[0],$mainCategoryTags[1],$mainCategoryTags[2]);
           
           $HashTags[] = array(   
                         'static_hashtags' => $mixedStatic,
                         'random_hashTags' => $RandomHashTags  
                         );

           // first three static hashtag for individual keyword in manual method
           $manualstatic = array($subCategoryTags[0],$subCategoryTags[1],$subCategoryTags[2]);
           $randomsub = array_rand($subCategoryTags,28);
           $manualhashtags = array();
           for($l=0;$l<count($randomsub);$l++) {
             $manualhashtags[] = $subCategoryTags[$randomsub[$l]];
           }
           $asdf = array_merge($manualstatic,$manualhashtags);
           $newtags = implode(',',$asdf);
           $manual_category = explode('__'.$clr.',',$newtags);
           

           $categories[] = array(
                           'name' => $keywords[$i],
                           'hashtag' => $manual_category   
                           );
       } else {

        // search in hashtag list of subcategories if not found in sub categories
        $SearchResult = $this->getallsubcategories();
        if($SearchResult){
          foreach ($SearchResult as $result) {
            $subcattags = explode(',',$result['hash_tags']);

            $searchKeyword = '#'.$keywords[$i];
            if(in_array($searchKeyword,$subcattags)) {
              $searchedhash = array($searchKeyword);
              //$searchedhashTags = array_unique(array_merge($searchedhash,$subcattags));
              $static = array_unique(array_merge($searchedhash,$subcattags));
              if($i == 0){ $clr ='#000000'; } elseif($i == 1){ $clr = '#0000FF'; } elseif($i == 2) { $clr = '#FF0000'; }
               $prefix = "_".$clr.',';
               $colored = implode("_" . $prefix,$static).$prefix;
               $searchedhashTags = explode(",",$colored);

              //$maincategoryTags = explode(',',$result['hash_tags']);
               $static2 = explode(',',$result['hash_tags']);
               $colored2 = implode("_" . $prefix,$static2).$prefix;
               $maincategoryTags = explode(",",$colored2);
               
              //Put searched hashtag at very first position and next three from subcategory and rest take randomly from sub and main category 
              //$mixedStatic = array($searchedhashTags[0],$searchedhashTags[1],$searchedhashTags[2],$searchedhashTags[3]);

              $mixedStatic = array_slice($searchedhashTags,0,4);
              $combinedHashTags = array_merge($searchedhashTags,$maincategoryTags);
              
              // Get random 24 Tags from sub and main category
              $randomindexes = array_rand($combinedHashTags,26);
              
               $RandomHashTags = array();
               for($k=0;$k<count($randomindexes);$k++) {
                 $RandomHashTags[] = $combinedHashTags[$randomindexes[$k]];
               }

              $HashTags[] = array( 
                         'static_hashtags' => $mixedStatic,
                         'random_hashTags' => $RandomHashTags  
                         );
              $randomsub = array_rand($subcattags,(30-count($mixedStatic)));  
              $manualhashtags = array();
               for($l=0;$l<count($randomsub);$l++) {
                 $manualhashtags[] = $subcattags[$randomsub[$l]];
               }

               $static3 = $manualhashtags;
               $colored3 = implode("_" . $prefix,$static3).$prefix;
               $maincategoryTags = explode(",",$colored3);


              $asdf = array_merge($mixedStatic,$maincategoryTags);
              $newtags = implode(',',$asdf);
              $manual_category = explode('__'.$clr.',',$newtags);

            
              $categories[] = array(
                           'name' => $keywords[$i],
                           'hashtag' => $manual_category   
                           ); 

            }
          }
        } else {
          return false;  
        }

       } 
     }
      
      $resultcount = count($HashTags);
      $staticcount = 6*$resultcount;
      $randomcount = 30-$staticcount;

      $check = array();
      
      //check if result found or not
      if($HashTags) {
    
          foreach($HashTags as $HashTag) {
            $check['allstatic'][] = $HashTag['static_hashtags'];
            $check['allrandom'][] = $HashTag['random_hashTags'];
          }


          // All static hashtags
          $combinedStatic = call_user_func_array('array_merge', $check['allstatic']);

          // All Random hashtags
          $combinedRandom = call_user_func_array('array_merge', $check['allrandom']); 
           
          // Combining static and random hasgtags to show relevant tags with the Limit
          $wsx = array_slice(array_unique(array_merge($combinedStatic,array_slice($combinedRandom,0,$randomcount))),0,$limit);
          
          $newwsx = implode(',',$wsx);
          $explode = explode(',',$newwsx);

          $relevent = array();
          $relevent_colors = array();
          for($d=0;$d<count($explode);$d++){
            $explode_element = explode('__',$explode[$d]);

            $relevent[] = $explode_element[0];
            if($explode_element[1]){ 
            $relevent_colors[] = $explode_element[1];
            } 
          }
          
          $data = array(
                  'categories' => $categories,
                  'relevent_tags' => $relevent,
                  'relevent_colors' => $relevent_colors 
                  );
          return $data; 
      } else {
        return false;
      }             

    }

    public function Search($keywords,$limit) { 
     $HashTags = array();
     $categories = array();  
     for($i=0;$i<count($keywords);$i++) 
     {

       $Result = $this->getindividualexacthashtags($keywords[$i]);
       if($Result) {
        $SearchResult = $Result;
       } else {
        $SearchResult = $this->getindividualexacthashtagslike($keywords[$i]); 
       }
       
       if($SearchResult) 
       { 
           // Separate Sub category Tags and Main category Tags
           //$subCategoryTags = explode(",",$SearchResult['hash_tags']);
           $static = explode(",",$SearchResult['hash_tags']);
           if($i == 0){ $clr ='#000000'; } elseif($i == 1){ $clr = '#0000FF'; } elseif($i == 2) { $clr = '#FF0000'; }
           $prefix = "_".$clr.',';
           $colored = implode("_" . $prefix,$static).$prefix;
           $subCategoryTags = explode(",",$colored);
           //print_r($subCategoryTags);
           
           //$mainCategoryTags = explode(",",$SearchResult['main_category_keywords']);
           $static2 = explode(",",$SearchResult['main_category_keywords']);
           $colored2 = implode("_" . $prefix,$static2).$prefix;
           $mainCategoryTags = explode(",",$colored2);
           
           // Combining both subcatgory and main category tags to provide random result after first 6 hash tags
           $combinedHashTags = array_merge($subCategoryTags,$mainCategoryTags);
           
           // Get random 24 Tags from sub and main category
           $randomindexes = array_rand($combinedHashTags,25);
           $RandomHashTags = array();
           for($k=0;$k<count($randomindexes);$k++) {
             $RandomHashTags[] = $combinedHashTags[$randomindexes[$k]];
           }
           
           // Make array of first static Hashtags from Sub and main category
           $mixedStatic = array($subCategoryTags[0],$subCategoryTags[1],$subCategoryTags[2],$mainCategoryTags[0],$mainCategoryTags[1],$mainCategoryTags[2]);
           
           $HashTags[] = array(   
                         'static_hashtags' => $mixedStatic,
                         'random_hashTags' => $RandomHashTags  
                         );

           // first three static hashtag for individual keyword in manual method
           $manualstatic = array($subCategoryTags[0],$subCategoryTags[1],$subCategoryTags[2]);
           $randomsub = array_rand($subCategoryTags,28);
           $manualhashtags = array();
           for($l=0;$l<count($randomsub);$l++) {
             $manualhashtags[] = $subCategoryTags[$randomsub[$l]];
           }
           $asdf = array_merge($manualstatic,$manualhashtags);
           $newtags = implode(',',$asdf);
           $manual_category = explode('__'.$clr.',',$newtags);
           

           $categories[] = array(
                           'name' => $keywords[$i],
                           'hashtag' => $manual_category   
                           );
       } else {

        // search in hashtag list of subcategories if not found in sub categories
        $SearchResult = $this->getallsubcategories();
        if($SearchResult){
          foreach ($SearchResult as $result) {
            $subcattags = explode(',',$result['hash_tags']);

            $searchKeyword = '#'.$keywords[$i];
            if(in_array($searchKeyword,$subcattags)) {
              $searchedhash = array($searchKeyword);
              //$searchedhashTags = array_unique(array_merge($searchedhash,$subcattags));
              $static = array_unique(array_merge($searchedhash,$subcattags));
              if($i == 0){ $clr ='#000000'; } elseif($i == 1){ $clr = '#0000FF'; } elseif($i == 2) { $clr = '#FF0000'; }
               $prefix = "_".$clr.',';
               $colored = implode("_" . $prefix,$static).$prefix;
               $searchedhashTags = explode(",",$colored);

              //$maincategoryTags = explode(',',$result['hash_tags']);
               $static2 = explode(',',$result['hash_tags']);
               $colored2 = implode("_" . $prefix,$static2).$prefix;
               $maincategoryTags = explode(",",$colored2);
               // print_r($maincategoryTags);
               // die;
              //Put searched hashtag at very first position and next three from subcategory and rest take randomly from sub and main category 
              //$mixedStatic = array($searchedhashTags[0],$searchedhashTags[1],$searchedhashTags[2],$searchedhashTags[3]);

              $mixedStatic = array_slice($searchedhashTags,0,4);
              $combinedHashTags = array_merge($searchedhashTags,$maincategoryTags);
              
              // Get random 24 Tags from sub and main category
              $randomindexes = array_rand($combinedHashTags,26);
              
               $RandomHashTags = array();
               for($k=0;$k<count($randomindexes);$k++) {
                 $RandomHashTags[] = $combinedHashTags[$randomindexes[$k]];
               }

              $HashTags[] = array( 
                         'static_hashtags' => $mixedStatic,
                         'random_hashTags' => $RandomHashTags  
                         );
              $randomsub = array_rand($subcattags,(30-count($mixedStatic)));  
              $manualhashtags = array();
               for($l=0;$l<count($randomsub);$l++) {
                 $manualhashtags[] = $subcattags[$randomsub[$l]];
               }
              
              $asdf = array_merge($mixedStatic,$manualhashtags);
              $newtags = implode(',',$asdf);
              $manual_category = explode('__'.$clr.',',$newtags);

            
              $categories[] = array(
                           'name' => $keywords[$i],
                           'hashtag' => $manual_category   
                           ); 

            }
          }
        } else {
          return false;  
        }

       } 
     }
      
      $resultcount = count($HashTags);
      $staticcount = 6*$resultcount;
      $randomcount = 30-$staticcount;

      $check = array();
      
      //check if result found or not
      if($HashTags) {
    
          foreach($HashTags as $HashTag) {
            $check['allstatic'][] = $HashTag['static_hashtags'];
            $check['allrandom'][] = $HashTag['random_hashTags'];
          }


          // All static hashtags
          $combinedStatic = call_user_func_array('array_merge', $check['allstatic']);

          // All Random hashtags
          $combinedRandom = call_user_func_array('array_merge', $check['allrandom']); 
           
          // Combining static and random hasgtags to show relevant tags with the Limit
          $wsx = array_slice(array_unique(array_merge($combinedStatic,array_slice($combinedRandom,0,$randomcount))),0,$limit);
          
          $newwsx = implode(',',$wsx);
          $explode = explode(',',$newwsx);

          $relevent = array();
          $relevent_colors = array();
          for($d=0;$d<count($explode);$d++){
            $explode_element = explode('__',$explode[$d]);

            $relevent[] = $explode_element[0];
            if($explode_element[1]){ 
            $relevent_colors[] = $explode_element[1];
            } 
          }
          
          $data = array(
                  'categories' => $categories,
                  'relevent_tags' => $relevent,
                  'relevent_colors' => $relevent_colors 
                  );
          return $data; 
      } else {
        return false;
      }         

    }


    public function getindividualexacthashtags($searchkey) {
       
     $queryDetails = new \yii\db\Query();
     $hashTags = $queryDetails->select('*')
        ->from('sub_categories')
        ->join('inner join', 'main_categories', 'sub_categories.main_category_id = main_categories.id')    
        ->where(['sub_categories.sub_category_name'=> $searchkey])
        //->where(['like','sub_categories.sub_category_name', $searchkey])
        ->one();

     return $hashTags;     
    }

    public function getindividualexacthashtagslike($searchkey) {
       
     $queryDetails = new \yii\db\Query();
     $hashTags = $queryDetails->select('*')
        ->from('sub_categories')
        ->join('inner join', 'main_categories', 'sub_categories.main_category_id = main_categories.id')    
        //->where(['sub_categories.sub_category_name'=> $searchkey])
        ->where(['like','sub_categories.sub_category_name', $searchkey])
        ->one();

     return $hashTags;     
    }

    public function getallsubcategories() {
       
     $queryDetails = new \yii\db\Query();
     $hashTags = $queryDetails->select('*')
        ->from('sub_categories')
        ->join('inner join', 'main_categories', 'sub_categories.main_category_id = main_categories.id')    
        //->where(['sub_categories.sub_category_name'=> $searchkey])
        //->where(['like','sub_categories.sub_category_name', $searchkey])
        ->all();

     return $hashTags;     
    }

}
