<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "main_categories".
 *
 * @property int $id
 * @property string $main_category_name
 * @property string $main_category_keywords
 *
 * @property SubCategories[] $subCategories
 */
class MainCategories extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'main_categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['main_category_name', 'main_category_keywords'], 'required'],
            [['main_category_keywords'], 'string'],
            [['main_category_name'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'main_category_name' => 'Main Category Name',
            'main_category_keywords' => 'Main Category Keywords',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubCategories()
    {
        return $this->hasMany(SubCategories::className(), ['main_category_id' => 'id']);
    }

    public function getParentCategories(){

        $parent_categories = MainCategories::find()
                            ->select(['id', 'main_category_name'])
                            ->asArray()
                            ->all();
        
        return $parent_categories;
    }

    public function getmaincategoryTags($cats) {
     //$cats = array(1,3);
     $parent_categories_tags = array();   
     for($i=0; $i < count($cats); $i++) { 
      $parent_categories_tags[] = MainCategories::find()
                            ->select(['main_category_keywords'])
                            ->where(['id'=>$cats[$i]])
                            ->asArray()
                            ->one();   
     }
     $tags = array();
     foreach($parent_categories_tags as $hashtags){
      $tags[] = array_slice(explode(',',$hashtags['main_category_keywords']),0,15); 
     }
     $mixedmaintags = call_user_func_array('array_merge',$tags);
     return $mixedmaintags;
    }
    
}
