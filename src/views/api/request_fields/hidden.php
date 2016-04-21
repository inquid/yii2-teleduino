<?php
use gogl92\teleduino\models\Request;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model Request */
/* @var $form ActiveForm */
/* @var $attributeName string */
?>
<?php echo Html::activeHiddenInput($model, $attributeName); ?>
