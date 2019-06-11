<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pages".
 *
 * @property int $id
 * @property string $Name
 * @property string $Content
 */
class Pages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'Name', 'Content'], 'required'],
            [['id'], 'integer'],
            [['Content'], 'string'],
            [['Name'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Name' => 'Name',
            'Content' => 'Content',
        ];
    }

     // get page content query
     public function getPage($page_id){

        $page = Pages::find()
                ->select(['name', 'content'])
                ->where(['id'=>$page_id])
                ->asArray()
                ->one();
        
        return $page;
    }
}
