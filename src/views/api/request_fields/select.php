<?php
use madand\teleduino\models\Request;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/* @var $this View */
/* @var $model Request */
/* @var $form ActiveForm */
/* @var $attributeName string */
?>

<?= $form->field($model, $attributeName)
    ->dropDownList(
        $model->extraAttributeFieldParam($attributeName, 'selectOptions'),
        ['prompt'=>'--Select value--']
    )->hint(nl2br($model->extraAttributeDescription($attributeName))) ?>
