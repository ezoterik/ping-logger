<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "{{%object}}".
 *
 * @property string $id
 * @property string $ip
 * @property integer $port
 * @property integer $port_udp
 * @property string $name
 * @property integer $group_id
 * @property integer $status
 * @property float $avg_rtt
 * @property bool $is_disable
 * @property string $updated
 *
 * @property \app\models\Group $group
 * @property \app\models\Log[] $logs
 */
class Object extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%object}}';
    }

    const STATUS_ERROR = 0;
    const STATUS_OK = 1;

    public static $statuses = [
        self::STATUS_ERROR => 'Ошибка',
        self::STATUS_OK => 'Ок',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['updated'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            //Удаляем связанные логи
            Log::deleteAll(['object_id' => $this->id]);

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
            [['ip', 'name', 'group_id'], 'required'],
            [['ip', 'name'], 'trim'],
            ['ip', 'string', 'max' => 15],
            ['ip', 'match', 'pattern' => '/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\/\d*)?$/'],
            [['port', 'port_udp', 'group_id'], 'integer', 'min' => 0],
            ['port', 'default', 'value' => 0],
            ['port_udp', 'default', 'value' => 0],
            [['name'], 'string', 'max' => 255],
            [['is_disable'], 'boolean'],
            [['group_id'], 'exist', 'targetClass' => Group::className(), 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ip' => Yii::t('app', 'IP'),
            'port' => Yii::t('app', 'Port TCP'),
            'port_udp' => Yii::t('app', 'Port UDP'),
            'name' => Yii::t('app', 'Title'),
            'group_id' => Yii::t('app', 'Group'),
            'status' => Yii::t('app', 'Status'),
            'avg_rtt' => Yii::t('app', 'Average RTT'),
            'is_disable' => Yii::t('app', 'Is Disable'),
            'updated' => Yii::t('app', 'Date of activity'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
        return $this->hasMany(Log::className(), ['object_id' => 'id'])->orderBy('created DESC');
    }
}
