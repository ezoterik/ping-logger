<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "objects".
 *
 * @property string $id
 * @property string $ip
 * @property integer $port
 * @property string $name
 * @property integer $type_id
 * @property integer $status
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
        return 'objects';
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
                    //ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'updated'],
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
            [['ip', 'port', 'name', 'type_id'], 'required'],
            [['port', 'type_id'], 'integer'],
            ['port', 'default', 'value' => 80],
            [['ip', 'name'], 'trim'],
            [['ip'], 'string', 'max' => 15],
            [['name'], 'string', 'max' => 255],
            [['is_disable'], 'boolean'],
            [['type_id'], 'exist', 'targetClass' => Group::className(), 'targetAttribute' => 'id'],
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
            'port' => Yii::t('app', 'Port'),
            'name' => Yii::t('app', 'Title'),
            'type_id' => Yii::t('app', 'Group'),
            'status' => Yii::t('app', 'Status'),
            'is_disable' => Yii::t('app', 'Is Disable'),
            'updated' => Yii::t('app', 'Date of activity'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
        return $this->hasMany(Log::className(), ['object_id' => 'id'])->orderBy('created DESC');
    }
}
