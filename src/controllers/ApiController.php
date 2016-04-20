<?php

namespace madand\teleduino\controllers;

use madand\teleduino\models\ApiOptionsForm;
use madand\teleduino\Module;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\HttpException;
use Yii;

/**
 * Class ApiController
 *
 * @property Module $module
 *
 * @author Andriy Kmit' <dev@madand.net>
 */
class ApiController extends Controller
{
    public function filters()
    {
        return ArrayHelper::merge(
            [
                'ajaxOnly + getMethodForm, sendApiRequest',
            ],
            parent::filters()
        );
    }

    /**
     * Display forms for API setup and requests.
     */
    public function actionIndex()
    {
        $api = $this->module->api;

        $apiOptionsForm = new ApiOptionsForm();
        $apiOptionsForm->api_endpoint = $api->getApiEndpoint();
        $apiOptionsForm->api_key = $api->getApiKey();

        if ($apiOptionsForm->load($_POST) && $apiOptionsForm->validate()) {
            $this->module->setApiEndpoint($apiOptionsForm->api_endpoint);
            $this->module->setApiKey($apiOptionsForm->api_key);
        }

        return $this->render(
            'index',
            [
                'apiOptionsForm' => $apiOptionsForm,
                'methodsListOptions' => $api->getListOptions(),
                'methodsDescriptions' => $api->getDescriptions(),
                'canSendRequest' => $api->canSendRequest(),
                'apiKeysOptions' => $this->module->getKeysListOptions(),
            ]
        );
    }

    /**
     * Get form with parameter fields according to specified method.
     *
     * @throws HttpException if invalid method was specified.
     */
    public function actionGetMethodForm()
    {
        $api = $this->module->api;

        $method = Yii::$app->request->post('method');
        try {
            $model = $api->createAndConfigureRequestModel($method);
            $model->setAttributes(
                [
                    'k' => $api->getApiKey(),
                    'r' => $method,
                ]
            );
        } catch (\Exception $e) {
            throw new HttpException(400, "Invalid method: $method.");
        }

        return $this->renderAjax(
            '_requestForm',
            [
                'model' => $model
            ]
        );
    }

    /**
     * Send request to Teleduino API and return HTML formatted response.
     *
     * @throws HttpException if invalid method was specified.
     */
    public function actionSendApiRequest()
    {
        $api = $this->module->api;

        $requestPostData = Yii::$app->request->post($api->getRequestModelClass());
        $method = isset($requestPostData['r']) ? $requestPostData['r'] : '';

        try {
            $model = $api->createAndConfigureRequestModel($method);
        } catch (\Exception $e) {
            throw new HttpException(400, "Invalid method: $method.");
        }

        if (!($model->load(Yii::$app->request->post()) && $model->validate())) {
            return Html::errorSummary($model);
        }

        // If user selected RAW(JSON) response format
        if ('pretty' !== Yii::$app->request->post('response_formatting')) {
            try {
                $responseText = $api->requestRawFromModel($model);
            } catch (\Exception $e) {
                $responseText = 'Error: ' . $e->getMessage();
            }

            return $this->renderPartial(
                '_responseRaw',
                ['rawResponse' => $responseText]
            );
        }

        $success  = false;
        $time     = null;
        try {
            $response = $api->requestFromModel($model);

            if ($response->isSuccessful()) {
                $success = true;
                $message = $api->formatResponseValuesHtml($method, $response->getValues());
                $time = $response->getTime();
            } else {
                $message = nl2br($response->getErrorMessage());
            }
        } catch (\Exception $e) {
            $message = 'Error: ' . $e->getMessage();
        }

        return $this->renderPartial(
            '_responsePretty',
            [
                'success' => $success,
                'message' => $message,
                'time' => $time,
            ]
        );
    }
}
