<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string $name
 * @property string $url URL-адрес с параметрами фильтра Avito
 * @property int $status Текущий статус задания (0 - в очереди, 1 - выполнено)
 * @property string $created_at
 * @property string $updated_at
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url'], 'string'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => 0],
            [['name', 'url'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'url' => 'URL',
            'status' => 'Статус',
            'statusValue' => 'Статус',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    public static function getStatusMap() {
        return [
            0 => 'В очереди',
            1 => 'Выполнено',
            2 => 'Выполняется'
        ];
    }

    public function getStatusValue() {
        return self::getStatusMap()[$this->status];
    }
}
