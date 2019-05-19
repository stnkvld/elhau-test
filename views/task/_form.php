<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Task */
/* @var $form yii\widgets\ActiveForm */
?>

<section class="tasks">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name', [
            'options' => ['class' => 'form-group'],
        ])->textInput([
            'class' => 'form-control'
        ]); ?>

        <?= $form->field($model, 'url', [
            'options' => ['class' => 'form-group'],
        ])->textInput([
            'class' => 'form-control'
        ]); ?>

    <div class="form-group">
        <?= Html::submitButton('Отправить в очередь', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</section>
