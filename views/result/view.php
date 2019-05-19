<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Result */

$this->title = $model->title;
\yii\web\YiiAsset::register($this);
?>
<div class="result-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'avito_url:url',
            'price',
            'address',
            'description',
            'created_at',
        ],
    ]) ?>

    <?php if (!empty($model->images)): ?>
        <h4>Фотографии</h4>
        <?= $model->images ?>
    <?php endif; ?>

    <?php if (!empty($model->params)): ?>
        <h4>Характеристики</h4>
        <?= $model->params ?>
    <?php endif; ?>

    <?php if (!empty($model->metro)): ?>
        <h4>Метро</h4>
        <?= $model->metro ?>
    <?php endif; ?>

</div>
