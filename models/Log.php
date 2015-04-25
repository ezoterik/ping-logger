<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "{{%log}}".
 *
 * @property string $object_id
 * @property integer $event_num
 * @property string $created
 *
 * @property \app\models\Object $object
 */
class Log extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%log}}';
    }

    const EVENT_ERROR = 0;
    const EVENT_GOOD = 1;

    public static $events = [
        self::EVENT_ERROR => 'Off',
        self::EVENT_GOOD => 'On',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created'],
                    //ActiveRecord::EVENT_BEFORE_UPDATE => ['updated'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['object_id', 'event_num'], 'required'],
            [['object_id', 'event_num'], 'integer'],
            [['event_num'], 'in', 'range' => array_keys(self::$events)],
            [['object_id'], 'exist', 'targetClass' => Object::className(), 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'object_id' => Yii::t('app', 'Object'),
            'event_num' => Yii::t('app', 'Event'),
            'created' => Yii::t('app', 'Date'),
        ];
    }

    public function getObject()
    {
        return $this->hasOne(Object::className(), ['id' => 'object_id']);
    }
}
