<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%group}}".
 *
 * @property integer $id
 * @property string $name
 * @property bool $is_disable
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
        return '{{%group}}';
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
            [['is_disable'], 'boolean'],
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
            'is_disable' => Yii::t('app', 'Is Disable'),
        ];
    }

    public function getObjects()
    {
        return $this->hasMany(Object::className(), ['group_id' => 'id']);
    }

    public static function getAllList()
    {
        $models = self::find()->asArray()->orderBy('name')->all();

        return ArrayHelper::map($models, 'id', 'name');
    }
}
