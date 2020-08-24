<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property string $object_id
 * @property int $event_num
 * @property int $created_at
 *
 * @property PingObject $object
 *
 * @mixin TimestampBehavior
 */
class Log extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%log}}';
    }

    public const EVENT_ERROR = 0;
    public const EVENT_GOOD = 1;

    public static array $events = [
        self::EVENT_ERROR => 'Off',
        self::EVENT_GOOD => 'On',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['object_id', 'event_num'], 'required'],
            [['object_id', 'event_num'], 'integer'],
            ['event_num', 'in', 'range' => array_keys(self::$events)],
            ['object_id', 'exist', 'targetClass' => PingObject::class, 'targetAttribute' => 'id'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'object_id' => Yii::t('app', 'Object'),
            'event_num' => Yii::t('app', 'Event'),
            'created_at' => Yii::t('app', 'Date'),
        ];
    }

    public function getObject(): ActiveQuery
    {
        return $this->hasOne(PingObject::class, ['id' => 'object_id']);
    }
}
