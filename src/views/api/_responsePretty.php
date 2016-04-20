<?php
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $success bool */
/* @var $message string */
/* @var $time string */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Response</h3>
    </div>
    <div class="panel-body">

    <?php if ($success): ?>
        <h3>Request was successful!</h3>

        <?php if ($message): ?>
            <?php echo $message; ?><br/>
        <?php endif ?>

        It took <b><?php echo Html::encode($time) ?></b> s.
    <?php else: ?>
        <h3>Request failed!</h3>

        <p><?php echo $message ?></p>
    <?php endif ?>
    </div>
</div>
