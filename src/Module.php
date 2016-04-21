<?php

namespace madand\teleduino;

use gogl92\teleduino\components\Api;
use gogl92\teleduino\console\HelpController;
use yii\base\InvalidConfigException;
use Yii;

/**
 * Class TeleduinoModule
 *
 * @property Api $api
 *
 * @author Andriy Kmit' <dev@madand.net>
 */
class Module extends \yii\base\Module
{
    /**
     * Session key for storing current API Endpoint.
     */
    const SESSION_API_ENDPOINT = 'teleduino_api_endpoint';

    /**
     * Session key for storing current API Key.
     */
    const SESSION_CURRENT_API_KEY = 'teleduino_current_api_key';

    public $defaultRoute = 'api';

    /**
     * List of available API keys.
     *
     * @var array
     */
    private $apiKeys = [];

    public function init()
    {
        parent::init();

        if (count($this->getApiKeys()) === 0) {
            throw new InvalidConfigException('You must provide at least one apiKey in the application configuration.');
        }

        $this->set('api', Yii::createObject(Api::className()));

        if ($this->inWebContext()) {
            // In web mode we store overriding values in session.
            $session = Yii::$app->session;

            if (isset($session[self::SESSION_API_ENDPOINT])) {
                $this->api->setApiEndpoint($session[self::SESSION_API_ENDPOINT]);
            }

            if (isset($session[self::SESSION_CURRENT_API_KEY])) {
                $this->api->setApiKey($session[self::SESSION_CURRENT_API_KEY]);
            }
        } else {
            // In console mode we override controllers with commands
            $this->controllerNamespace = __NAMESPACE__ . '\commands';
            Yii::$app->controllerMap['help'] = HelpController::className();
        }
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            // Place access checking code here, if needed.
            return true;
        } else {
            return false;
        }
    }


    public function getName()
    {
        return 'Teleduino API';
    }

    /**
     * Set API Endpoint for {@link $api} component. Also store it in session, if in WEB mode.
     *
     * @param string $apiEndpoint
     */
    public function setApiEndpoint($apiEndpoint)
    {
        $this->api->setApiEndpoint($apiEndpoint);

        if ($this->inWebContext()) {
            Yii::$app->session[self::SESSION_API_ENDPOINT] = $apiEndpoint;
        }
    }

    /**
     * Set API Key for {@link $api} component. Also store it in the session, if in WEB mode.
     *
     * @param $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->api->setApiKey($apiKey);

        if ($this->inWebContext()) {
            Yii::$app->session[self::SESSION_CURRENT_API_KEY] = $apiKey;
        }
    }

    /**
     * @param array $apiKeys
     */
    public function setApiKeys(array $apiKeys)
    {
        $this->apiKeys = $apiKeys;
    }

    /**
     * @return array
     */
    public function getApiKeys()
    {
        return $this->apiKeys;
    }

    /**
     * Get list options array of available api keys, suitable for feeding into {@link Html::dropDownList()}.
     * @return array
     */
    public function getKeysListOptions()
    {
        $result = [];
        foreach ($this->getApiKeys() as $data) {
            $name = isset($data['name']) && strlen($data['name']) > 0 ? "{$data['name']} ({$data['key']})" : $data['key'];
            $result[$data['key']] = $name;
        }

        return $result;
    }

    /**
     * @return bool Returns TRUE if app is running in WEB context.
     */
    private function inWebContext()
    {
        return Yii::$app instanceof \yii\web\Application;
    }
}
