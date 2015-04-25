<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%group}}".
 *
 * @property integer $id
 * @property string $name
 * @property bool $is_disable
 * @property string $lock_date
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
            'lock_date' => Yii::t('app', 'Lock Date'),
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

    /**
     * Ставит отметку о блокировке для всех указанных групп
     * (не 100% способ от защиты параллельных запусков)
     *
     * @param array $groupIds
     */
    public static function lock(array $groupIds)
    {
        if (count($groupIds) == 0) {
            return;
        }

        self::updateAll(['lock_date' => new Expression('NOW()')], ['id' => $groupIds]);
    }

    /**
     * Уберает у группы защиту от параллельного пингования группы
     *
     * @param int $groupId
     */
    public static function unLock($groupId)
    {
        self::updateAll(['lock_date' => '0000-00-00 00:00:00'], ['id' => $groupId]);
    }
}
