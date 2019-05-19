<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "results".
 *
 * @property int $id
 * @property int $task_id
 * @property string $avito_url
 * @property array $data
 * @property string $created_at
 *
 * @property Tasks $task
 */
class Result extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'results';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'avito_url', 'data'], 'required'],
            [['task_id'], 'integer'],
            [['avito_url'], 'string', 'max' => 255],
            [['data'], 'safe'],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::className(), 'targetAttribute' => ['task_id' => 'id']],
            [['created_at'], 'safe'],
            [['avito_url'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Задание',
            'avito_url' => 'Avito-URL',
            'data' => 'Данные',
            'created_at' => 'Создано',
            'title' => 'Заголовок',
            'price' => 'Цена',
            'address' => 'Адрес',
            'params' => 'Характеристики',
            'metro' => 'Метро',
            'images' => 'Фотографии',
            'description' => 'Описание',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::className(), ['id' => 'task_id']);
    }

    public function getTaskName() {
        return $this->task->name;
    }

    public function getDataArray() {
        return json_decode($this->data, true);
    }

    public function getTitle() {
        return $this->getDataArray()['title'];
    }

    public function getPrice() {
        return $this->getDataArray()['price'];
    }

    public function getAddress() {
        return $this->getDataArray()['address'];
    }

    public function getParams() {
        $params = $this->getDataArray()['params'];

        $res = '';
        foreach ($params as $param) {
            $res .= "<li>$param</li>";
        }

        return !empty($res) ? "<ul>$res</ul>" : null;
    }

    public function getMetro() {
        $params = $this->getDataArray()['metro'];

        $res = '';
        foreach ($params as $param) {
            $res .= "<li>$param</li>";
        }

        return !empty($res) ? "<ul>$res</ul>" : null;
    }

    public function getImages() {
        $params = $this->getDataArray()['images'];

        $res = '';
        foreach ($params as $param) {
            $res .= "<li><img src='$param'></li>";
        }

        return !empty($res) ? "<ul>$res</ul>" : null;
    }

    public function getDescription() {
        return $this->getDataArray()['description'];
    }
}
