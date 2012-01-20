<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "types".
 *
 * @property integer $id
 * @property string $name
 *
 * @property \app\models\Object[] $objects
 */
class Group extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'types';
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            //Удаляем вложенные объекты
            foreach ($this->objects as $object) {
                $object->delete();
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'trim'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Title'),
        ];
    }

    public function getObjects()
    {
        return $this->hasMany(Object::className(), ['type_id' => 'id']);
    }

    public static function getAllList()
    {
        $models = self::find()->asArray()->orderBy('name')->all();

        return ArrayHelper::map($models, 'id', 'name');
    }
}
