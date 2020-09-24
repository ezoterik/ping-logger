<?php

namespace app\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property string $id
 * @property string $ip
 * @property int $port
 * @property int $port_udp
 * @property string $name
 * @property string $address
 * @property string $note
 * @property int $group_id
 * @property int $status
 * @property float $avg_rtt
 * @property bool $is_disable
 * @property int $updated_at
 *
 * @property Group $group
 * @property Log[] $logs
 *
 * @mixin TimestampBehavior
 */
class PingObject extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%object}}';
    }

    public const STATUS_ERROR = 0;
    public const STATUS_OK = 1;

    public static array $statuses = [
        self::STATUS_ERROR => 'Ошибка',
        self::STATUS_OK => 'Ок',
    ];

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            'typecast' => [
                'class' => AttributeTypecastBehavior::class,
                'typecastAfterFind' => true,
            ],
        ];
    }

    public function beforeDelete(): bool
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        //TODO: Перевести на вторичные ключи
        //Удаляем связанные логи
        Log::deleteAll(['object_id' => $this->id]);

        return true;
    }

    public function rules(): array
    {
        return [
            [['ip', 'name', 'address', 'note'], 'trim'],
            [['ip', 'name', 'group_id'], 'required'],
            ['ip', 'string', 'max' => 15],
            ['ip', 'match', 'pattern' => '/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\/\d*)?$/'],
            [['port', 'port_udp', 'group_id'], 'integer', 'min' => 0],
            ['port', 'default', 'value' => 0],
            ['port_udp', 'default', 'value' => 0],
            [['name', 'address', 'note'], 'string', 'max' => 255],
            ['is_disable', 'boolean'],
            ['group_id', 'exist', 'targetClass' => Group::class, 'targetAttribute' => 'id'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ip' => Yii::t('app', 'IP'),
            'port' => Yii::t('app', 'Port TCP'),
            'port_udp' => Yii::t('app', 'Port UDP'),
            'name' => Yii::t('app', 'Title'),
            'address' => Yii::t('app', 'Address'),
            'note' => Yii::t('app', 'Note'),
            'group_id' => Yii::t('app', 'Group'),
            'status' => Yii::t('app', 'Status'),
            'avg_rtt' => Yii::t('app', 'Average RTT'),
            'is_disable' => Yii::t('app', 'Is disable'),
            'updated_at' => Yii::t('app', 'Date of activity'),
        ];
    }

    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(Group::class, ['id' => 'group_id']);
    }

    public function getLogs(): ActiveQuery
    {
        return $this->hasMany(Log::class, ['object_id' => 'id'])->orderBy(['created_at' => SORT_DESC]);
    }
}
