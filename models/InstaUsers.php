<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "insta_users".
 *
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $password
 */

class InstaUsers extends \yii\db\ActiveRecord 
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'insta_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'username', 'password'], 'required'],
            [['name'], 'string', 'max' => 20],
            [['username'], 'string', 'max' => 12],
            [['password'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'username' => 'Username',
            'password' => 'Password',
        ];
    }



    
}
