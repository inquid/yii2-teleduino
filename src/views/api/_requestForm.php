<?php
use gogl92\teleduino\models\Request;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model Request */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'id' => 'request-form',
    'action'=>['send-api-request'],
    'layout'=>'horizontal',
        'fieldConfig'=>[
            'horizontalCssClasses'=>[
                'label' => 'col-sm-2',
                'wrapper' => 'col-sm-6',
                'hint' => 'col-sm-4',
            ],
        ],
]); ?>

    <?php foreach ($model->getExtraAttributes() as $attributeName): ?>
        <?= $this->render(
            'request_fields/' . $model->extraAttributeFieldType($attributeName),
            [
                'model'=>$model,
                'form'=>$form,
                'attributeName'=>$attributeName,
            ]
        ) ?>
    <?php endforeach; ?>

    <div class="form-group required">
        <?= Html::label('Response format', 'response_formatting', ['class'=>'control-label col-sm-2']) ?>
        <div class="col-sm-6">
            <?= Html::hiddenInput('response_formatting', '') ?>
            <div class="radio">
                <?= Html::radio('response_formatting', true, ['label'=>'User friendly', 'value'=>'pretty']) ?>
            </div>
            <div class="radio">
                <?= Html::radio('response_formatting', false, ['label'=>'Raw (JSON)', 'value'=>'raw']) ?>
            </div>
        </div>
    </div>

    <div class="col-sm-offset-2">
        <?php foreach ($model->coreAttributeNames() as $attributeName) {
            echo Html::activeHiddenInput($model, $attributeName);
        } ?>

        <?php echo Button::widget([
                'label'=>'Send request!',
                'options'=>['class'=>'btn-primary']
        ]); ?>
    </div>

<?php ActiveForm::end() ?>
