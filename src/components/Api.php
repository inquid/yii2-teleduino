<?php

namespace madand\teleduino\components;

use madand\teleduino\models\Request;
use madand\teleduino\models\Response;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Class TeleduinoApi encapsulates data about all the available methods of Teleduino API v328.
 * It also relies on unirest-php library for performing actual HTTP requests.
 *
 * @property string $apiEndpoint URI of the API endpoint (request URL). Defaults to 'https://us01.proxy.teleduino.org/api/1.0/328.php'.
 * @property string $apiKey The API key to be used in requests.
 * @property array $availableMethods array of the names of all available API methods.
 * @property array $descriptions array of the descriptions of all available API methods.
 *
 * @author Andriy Kmit' <dev@madand.net>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
class Api extends Component
{
    /**
     * @var string
     */
    private $apiEndpoint = 'https://us01.proxy.teleduino.org/api/1.0/328.php';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var array
     */
    private $methodsData = [];

    /**
     * @var array
     */
    private $listOptions = [];

    /**
     * @var array
     */
    private $descriptions = [];

    /**
     * @var string
     */
    private $userAgent = 'Yii-teleduino/1.0';

    /**
     * @var bool
     */
    private $sslVerifyPeer = true;

    /**
     * @var string
     */
    private $requestModelClass = 'Request';

    /**
     * @var array
     */
    private $availableMethods = [];

    /**
     * @var array
     */
    private $methodParams = [];

    /**
     * @param string $apiEndpoint
     */
    public function setApiEndpoint($apiEndpoint)
    {
        $this->apiEndpoint = $apiEndpoint;
    }

    /**
     * @return string
     */
    public function getApiEndpoint()
    {
        return $this->apiEndpoint;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param boolean $sslVerifyPeer
     */
    public function setSslVerifyPeer($sslVerifyPeer)
    {
        $this->sslVerifyPeer = $sslVerifyPeer;
    }

    /**
     * @return boolean
     */
    public function getSslVerifyPeer()
    {
        return $this->sslVerifyPeer;
    }

    /**
     * @param string $requestModelClass
     */
    public function setRequestModelClass($requestModelClass)
    {
        $this->requestModelClass = $requestModelClass;
    }

    /**
     * @return string
     */
    public function getRequestModelClass()
    {
        return $this->requestModelClass;
    }

    public function init()
    {
        parent::init();

        $this->methodsData = require(__DIR__ . DIRECTORY_SEPARATOR . '_methods_data.php');
    }

    /**
     * Get list options array of available methods, suitable for feeding into {@link Html::dropDownList()}.
     *
     * @return array
     */
    public function getListOptions()
    {
        if (empty($this->listOptions)) {
            $this->processMethodsData();
        }

        return $this->listOptions;
    }

    /**
     * Get array with the descriptions of methods.
     *
     * @return array Array with the following structure: ['method'=>'description'].
     */
    public function getDescriptions()
    {
        if (empty($this->descriptions)) {
            $this->processMethodsData();
        }

        return $this->descriptions;
    }

    /**
     * Get array with the descriptions of methods.
     *
     * @return array Array with the following structure: ['method'=>'description'].
     */
    public function getAvailableMethods()
    {
        if (empty($this->availableMethods)) {
            $this->processMethodsData();
        }

        return $this->availableMethods;
    }

    public function getMethodParams($method)
    {
        if (empty($this->methodParams)) {
            $this->processMethodsData();
        }

        return $this->methodParams[$method];
    }

    /**
     * Check whether all options, required to send requests, are set.
     *
     * @return bool
     */
    public function canSendRequest()
    {
        return isset($this->apiEndpoint, $this->apiKey);
    }

    /**
     * Create request model and augment it with additional attributes and validation rules, according to method's definition.
     *
     * @param string $method method name.
     * @return Request request model instance.
     */
    public function createAndConfigureRequestModel($method)
    {
        $methodConfig = $this->getMethodConfig($method);

        /** @var $requestModel Request */
        $requestModel = new Request();

        // Augment model with extra attributes and theirs supplemental data, if any.
        if (isset($methodConfig['requestParams']) && !empty($methodConfig['requestParams'])) {
            $descriptions = $rules = $fieldTypes = $fieldParams = [];

            foreach ($methodConfig['requestParams'] as $attributeName => $config) {
                $descriptions[$attributeName] = $config['description'];

                if (isset($config['fieldType'])) {
                    $fieldTypes[$attributeName] = $config['fieldType'];

                    if (isset($config['fieldParams'])) {
                        $fieldParams[$attributeName] = $config['fieldParams'];
                    }
                }

                if (is_array($config['validators'][0])) {
                    $rules = ArrayHelper::merge(
                        $rules,
                        $config['validators']
                    );
                } else {
                    $rules = ArrayHelper::merge(
                        $rules,
                        [$config['validators']]
                    );
                }
            }

            $requestModel->setExtraAttributes(array_keys($methodConfig['requestParams']));
            $requestModel->setExtraAttributesDescriptions($descriptions);
            $requestModel->setExtraRules($rules);
            $requestModel->setExtraAttributesFieldTypes($fieldTypes);
            $requestModel->setExtraAttributesFieldParams($fieldParams);
        }

        return $requestModel;
    }

    /**
     * Perform request to Teleduino API.
     * Parameters are not checked for correctness!
     *
     * @param array $params request parameters.
     * @return Response
     */
    public function request($params)
    {
        $response = $this->requestInternal($params);

        return new Response($response->code, $response->body);
    }

    /**
     * Perform request to Teleduino API, supplying the attributes of the given model as params.
     *
     * @param Request $model
     * @return Response
     * @see request()
     */
    public function requestFromModel($model)
    {
        return $this->request($model->getAttributes());
    }

    /**
     * Perform request to Teleduino API. Returns raw response text.
     * Parameters are not checked for correctness!
     *
     * @param array $params request parameters.
     * @return string raw response text.
     */
    public function requestRaw($params)
    {
        $response = $this->requestInternal($params);

        return $response->raw_body;
    }

    /**
     * Perform request to Teleduino API, supplying the attributes of the given model as params.
     * Returns raw response text.
     *
     * @param Request $model
     * @return string raw response text.
     * @see request()
     */
    public function requestRawFromModel($model)
    {
        return $this->requestRaw($model->getAttributes());
    }

    /**
     * Transform response 'values' array data into user readable format.
     * @param string $method
     * @param string $values
     * @return string
     */
    public function formatResponseValues($method, $values)
    {
        if (is_array($values) && !empty($values)) {
            $methodConfig = $this->getMethodConfig($method);

            if (isset($methodConfig['formatResponseValues'])) {
                return call_user_func($methodConfig['formatResponseValues'], $values);
            }
        }

        return null;
    }

    /**
     * Transform response 'values' array data into user readable format, HTML formatted.
     * @param string $method
     * @param string $values
     * @return string
     */
    public function formatResponseValuesHtml($method, $values)
    {
        if (is_array($values) && !empty($values)) {
            $methodConfig = $this->getMethodConfig($method);

            if (isset($methodConfig['formatResponseValuesHtml'])) {
                return call_user_func($methodConfig['formatResponseValuesHtml'], $values);
            }

            return $this->formatResponseValues($method, $values);
        }

        return null;
    }

    /**
     * Extract list options and descriptions out of the {@link methodsData} array and fill in the corresponding properties.
     */
    private function processMethodsData()
    {
        foreach ($this->methodsData as $group => $methods) {
            foreach ($methods as $method => $details) {
                if (!isset($this->listOptions[$group])) {
                    $this->listOptions[$group] = [];
                }
                $this->listOptions[$group][$method] = $method;

                $this->availableMethods[] = $method;
                $this->descriptions[$method] = $details['description'];

                if (isset($details['requestParams'])) {
                    $this->methodParams[$method] = array_keys($details['requestParams']);
                } else {
                    $this->methodParams[$method] = [];
                }
            }
        }
    }

    /**
     * Get configuration array for the given method.
     *
     * @param string $method
     * @throws \InvalidArgumentException
     * @return array|null method configuration array or NULL, if method not found.
     */
    private function getMethodConfig($method)
    {
        foreach ($this->methodsData as $methods) {
            if (isset($methods[$method])) {
                return $methods[$method];
            }
        }

        throw new \InvalidArgumentException("Invalid method '$method'.");
    }

    /**
     * Actual request implementation.
     * @param $params
     * @return \Unirest\Response
     */
    private function requestInternal($params)
    {
        \Unirest\Request::verifyPeer((bool)$this->sslVerifyPeer);

        /** @var $response \Unirest\Response */
        $response = \Unirest\Request::get(
            $this->apiEndpoint,
            [
                'user-agent' => $this->userAgent,
            ],
            $params
        );

        return $response;
    }
}
