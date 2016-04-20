<?php

use madand\teleduino\Asset;
use madand\teleduino\models\ApiOptionsForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/* @var $this View */
/* @var $apiOptionsForm ApiOptionsForm */
/* @var $methodsListOptions array */
/* @var $canSendRequest bool */
/* @var $form ActiveForm */
/* @var $methodsDescriptions array */
/* @var $apiKeysOptions array */

Asset::register($this);

$moduleName = $this->context->module->name;

$this->params['breadcrumbs'][] = $moduleName;

$this->registerJs(
    'window.yii_teleduino.methodsDescriptions = ' . Json::encode($methodsDescriptions),
    View::POS_END
);
?>

<h1><?php echo $moduleName ?></h1>

<div id="yii-teleduino">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">API Options</h3>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                'id'=>'api-options-form',
                'options'=>['class'=>'api-options-form'],
                'layout'=>'horizontal',
            ]); ?>

                <?php echo $form->errorSummary($apiOptionsForm); ?>

                <?= $form->field($apiOptionsForm, 'api_endpoint') ?>
                <?= $form->field($apiOptionsForm, 'api_key')
                    ->dropDownList($apiKeysOptions, ['prompt'=>'-- Select key --'])?>

                <div class="form-group ">
                    <div class="col-sm-offset-3">
                        <?= Button::widget([
                                'label'=>'Save options',
                                'options'=>['class'=>'btn-primary', 'type'=>'submit']
                            ]) ?>
                        <?= Button::widget([
                                'label'=>'Reset form',
                                'options'=>['class'=>'btn-danger', 'type'=>'reset']
                            ]) ?>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>


    <?php if ($canSendRequest): ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Request Options</h3>
        </div>
        <div class="panel-body request-options-block">
            <div class="form-horizontal">
                <div class="form-group">
                    <?php echo Html::label('Method', 'req_method', ['class'=>'control-label col-sm-2']); ?>
                    <div class="col-sm-6">
                        <?= \vova07\select2\Widget::widget([
                                'name' => 'req_method',
                                'items'=>[''=>''] + $methodsListOptions,
                                'options' => [
                                    'style' => 'width: 250px',
                                ],
                                'settings' => [
                                    'placeholder' => 'Select request Method',
                                ],
                                'events'=>[
                                    'change'=>new JsExpression('yii_teleduino.onSelectChange'),
                                ],
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div id="teleduino-method-description"
                         class="col-sm-offset-2 col-sm-6 alert alert-info"></div>

                    <?php echo Html::hiddenInput(
                        'teleduino-get-method-form-url',
                        Url::to(['get-method-form'], true),
                        ['id'=>'teleduino-get-method-form-url']
                    ) ?>
                </div>
            </div>

            <div id="teleduino-request-form-container"></div>
        </div>
    </div>

    <div id="teleduino-response-container"></div>
    <?php endif; ?>
</div>

<?php
