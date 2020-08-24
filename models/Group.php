<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property bool $is_disable
 * @property int $lock_at
 *
 * @property PingObject[] $objects
 */
class Group extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%group}}';
    }

    public function beforeDelete(): bool
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        //TODO: Перевести на вторичные ключи
        //Удаляем вложенные объекты
        foreach ($this->objects as $object) {
            $object->delete();
        }

        return true;
    }

    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'trim'],
            ['name', 'string', 'max' => 255],
            ['name', 'unique'],
            ['is_disable', 'boolean'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Title'),
            'is_disable' => Yii::t('app', 'Is disable'),
            'lock_at' => Yii::t('app', 'Lock date'),
        ];
    }

    public function getObjects(): ActiveQuery
    {
        return $this->hasMany(PingObject::class, ['group_id' => 'id']);
    }

    public static function getAllList(): array
    {
        return self::find()
            ->select('name')
            ->orderBy('name')
            ->indexBy('id')
            ->asArray()
            ->column();
    }

    /**
     * Ставит отметку о блокировке для всех указанных групп
     * (не 100% способ от защиты параллельных запусков)
     *
     * @param array $groupIds
     */
    public static function lock(array $groupIds): void
    {
        if (!$groupIds) {
            return;
        }

        self::updateAll(
            ['lock_at' => time()],
            ['id' => $groupIds]
        );
    }

    /**
     * Убирает у группы защиту от параллельного пингования группы
     *
     * @param int $groupId
     */
    public static function unLock(int $groupId): void
    {
        self::updateAll(
            ['lock_at' => null],
            ['id' => $groupId]
        );
    }

    /**
     * Убирает у групп блокировку, если блокировка висит слишком долго (зависла)
     */
    public static function unLockOld(): void
    {
        self::updateAll(
            ['lock_at' => null],
            'lock_at IS NOT NULL AND lock_at < (UNIX_TIMESTAMP() - ' . (UTC_HOUR * 15) . ')'
        );
    }
}
