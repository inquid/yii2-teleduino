<?php

namespace madand\teleduino\commands;

use Exception;
use madand\teleduino\console\ConsoleAction;
use madand\teleduino\models\Request;
use madand\teleduino\Module;
use Yii;
use yii\base\UnknownPropertyException;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * Class ApiController implements console command interface for the yii-teleduino module.
 *
 * @property array $allowedApiOptions
 *
 * @author Andriy Kmit' <dev@madand.net>
 */
class ApiController extends Controller
{
    public $color = true;
    public $interactive = false;
    public $defaultAction = '';

    public $responseFormat = 'pretty';

    /**
     * @var array
     */
    private $allowedApiOptions = ['apiEndpoint', 'apiKey', 'userAgent', 'sslVerifyPeer'];

    /**
     * @var \madand\teleduino\components\Api
     */
    private $api;

    public function init()
    {
        parent::init();

        $this->api = Module::getInstance()->api;

        foreach ($this->allowedApiOptions as $option) {
            $this->$option = null;
        }

    }

    public function actions()
    {
        $result = [];
        foreach ($this->api->availableMethods as $method) {
            // We override {@link createAction()} so no actual value is needed here.
            $result[$method] = null;
        }

        return $result;
    }

    public function createAction($id)
    {
        try {
            $requestModel = $this->api->createAndConfigureRequestModel($id);

            return Yii::createObject(
                [
                    'class'=>ConsoleAction::className(),
                    'model'=>$requestModel,
                ],
                [$id, $this]
            );
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    public function runAction($id, $params = [])
    {
        // Create attributes for all options available for the given API method.
        $options = $this->options($id === '' ? $this->defaultAction : $id);
        foreach ($options as $option) {
            $this->$option = '';
        }

        return parent::runAction($id, $params);
    }

    public function getHelpSummary()
    {
        return 'Provides console interface to the Teleduino API (v328).';
    }

    public function getHelp()
    {
        $result = $this->getHelpSummary();

        return $result;
    }

    /**
     * @param ConsoleAction $action
     * @return string
     */
    public function getActionHelpSummary($action)
    {
        return $this->api->descriptions[$action->id];
    }

    /**
     * @param ConsoleAction $action
     * @return string
     */
    public function getActionHelp($action)
    {
        return $this->api->descriptions[$action->id];
    }

    /**
     * @param ConsoleAction $action
     * @return array
     */
    public function getActionOptionsHelp($action)
    {
        $result = [];
        foreach ($action->model->getExtraAttributes() as $option) {
            $result[$option] = [
                'type'=>null,
                'default'=>null,
                'comment'=>$action->model->extraAttributeDescription($option)
            ];
        }

        return $result;
    }

    public function getActionArgsHelp($action)
    {
        return [];
    }

    public function options($actionID)
    {
        return ArrayHelper::merge(
            ['responseFormat'],
            $this->allowedApiOptions,
            $this->api->getMethodParams($actionID)
        );
    }

    public function getGlobalOptionsHelp()
    {
        return Console::renderColoredString(<<<HEREDOC

GLOBAL OPTIONS (for all methods):

    %r--responseFormat%n
        Possible values:
          %ypretty%n - format response in human readable representation.
          %yjson%n   - raw JSON, as received from the server.
        Default: %ypretty%n

    %r--apiEndpoint%n
    %r--apiKey%n
    %r--userAgent%n
    %r--sslVerifyPeer%n
HEREDOC
        , $this->color);
    }

    public function __set($name, $value)
    {
        // Allow dynamic addition of properties to controller.
        try {
            parent::__set($name, $value);
        } catch (UnknownPropertyException $e) {
            $this->$name = $value;
        }
    }

    /**
     * @return array
     */
    public function getAllowedApiOptions()
    {
        return $this->allowedApiOptions;
    }
}
