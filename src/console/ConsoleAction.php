<?php

namespace gogl92\teleduino\console;

use gogl92\teleduino\commands\ApiController;
use gogl92\teleduino\components\Api;
use gogl92\teleduino\models\Request;
use gogl92\teleduino\Module;
use yii\base\Action;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class ConsoleAction
 *
 * @package gogl92\teleduino\components
 * @author Andriy Kmit' <dev@madand.net>
 */
class ConsoleAction extends Action
{
    /**
     * @var Request
     */
    public $model;

    /**
     * @var ApiController
     */
    public $controller;

    public function run()
    {
        $apiMethod = $this->id;
        /** @var $api Api */
        $api = Module::getInstance()->api;
        $requestModel = $this->model;

        // Walk through options and assign them into corresponding objects.
        $methodAttributes = $requestModel->getExtraAttributes();
        $responseFormat = 'pretty';
        foreach ($this->controller->getOptionValues($this->id) as $name=>$value) {
            if (in_array($name, $methodAttributes)) {
                $requestModel->{$name} = $value;
            } elseif (!empty($value) && in_array($name, $this->controller->allowedApiOptions)) {
                $apiSetter = 'set' . $name;
                $api->{$apiSetter}($value);
            } elseif ('responseFormat' === $name) {
                if ('pretty' === $value || 'json' === $value) {
                    $responseFormat = $value;
                }
                else {
                    $this->controller->stderr(
                        Console::renderColoredString(
                            "Error: Invalid value %y$value%n for %r--responseFormat%n.
Valid values are: %ypretty%n and %yjson%n\n",
                            $this->controller->color
                        )
                    );

                    return Controller::EXIT_CODE_ERROR;
                }
            }
        }

        if (null === $api->getApiEndpoint()) {
            $this->controller->stderr(
                Console::renderColoredString(
                    "Error: %r--apiEndpoint%n was not specified.\n\n"
                ),
                $this->controller->color
            );

            return Controller::EXIT_CODE_ERROR;
        }

        if (null === $api->getApiKey()) {
            $this->controller->stderr(
                Console::renderColoredString(
                    "Error: %r--apiKey%n was not specified.\n\n"
                ),
                $this->controller->color
            );

            return Controller::EXIT_CODE_ERROR;
        }

        $requestModel->r = $apiMethod;
        $requestModel->k = $api->apiKey;

        // Check whether all the parameter are OK for doing request
        if (!$requestModel->validate()) {
            $errors = $requestModel->getErrors();

            $this->controller->stderr("Validation errors:\n");

            foreach ($errors as $attribute => $attrErrors) {
                $this->controller->stderr(
                    Console::renderColoredString("  %r--$attribute%n\n"),
                    $this->controller->color
                );

                foreach ($attrErrors as $errMsg) {
                    $this->controller->stderr("    $errMsg\n");
                }

                $this->controller->stderr("\n");
            }

            return Controller::EXIT_CODE_ERROR;
        }

        // If user selected RAW(JSON) response format
        if ('pretty' !== $responseFormat) {
            try {
                $responseText = $api->requestRawFromModel($requestModel);
            } catch (\Exception $e) {
                $responseText = 'Error: ' . $e->getMessage();
            }

            $this->controller->stdout($responseText . "\n");

            return Controller::EXIT_CODE_NORMAL;
        }

        $success  = false;
        $time     = null;
        try {
            $response = $api->requestFromModel($requestModel);

            if ($response->isSuccessful()) {
                $success = true;
                $message = $api->formatResponseValues($apiMethod, $response->getValues());
                $time    = $response->getTime();
            } else {
                $message = $response->getErrorMessage();
            }
        } catch (\Exception $e) {
            $message = 'Error: ' . $e->getMessage();
        }

        $this->controller->stdout(($success ? 'Request was successful!' : 'Request failed!') . "\n\n");
        $this->controller->stdout("$message\n");
        $this->controller->stdout(isset($time) ? "It took: $time s.\n" : '');

        return Controller::EXIT_CODE_NORMAL;
    }
}
