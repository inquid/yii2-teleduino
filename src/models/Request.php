<?php

namespace madand\teleduino\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class Request implements model with optional dynamically defined attributes and their validation rules.
 *
 * @author Andriy Kmit' <dev@madand.net>
 */
class Request extends Model
{
    /**
     * The unique API key for the device.
     * @var string
     */
    public $k;
    /**
     * The method being requested.
     * @var string
     */
    public $r;
    /**
     * The output format. Possible values are json, jsonp or php.
     * @var string
     */
    public $o = 'json';

    private $extraAttributes = [];

    private $extraAttributesData = [];

    private $extraAttributesDescriptions = [];

    private $extraAttributesFieldTypes = [];

    private $extraAttributesFieldParams = [];

    private $extraRules = [];

    public function __get($name) {
       if (array_key_exists($name, array_flip($this->extraAttributes))) {
           if (isset($this->extraAttributesData[$name])) {
               return $this->extraAttributesData[$name];
           } else {
               return null;
           }
       }

       return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if (array_key_exists($name, array_flip($this->extraAttributes))) {
            $this->extraAttributesData[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @param array $extraAttributes
     */
    public function setExtraAttributes(array $extraAttributes)
    {
        $this->extraAttributes = $extraAttributes;
    }

    /**
     * @return array
     */
    public function getExtraAttributes()
    {
        return $this->extraAttributes;
    }

    /**
     * @param array $extraRules
     */
    public function setExtraRules(array $extraRules)
    {
        $this->extraRules = $extraRules;
    }

    /**
     * @return array
     */
    public function getExtraRules()
    {
        return $this->extraRules;
    }

    /**
     * @param array $extraAttributesDescriptions
     */
    public function setExtraAttributesDescriptions(array $extraAttributesDescriptions)
    {
        $this->extraAttributesDescriptions = $extraAttributesDescriptions;
    }

    /**
     * @return array
     */
    public function getExtraAttributesDescriptions()
    {
        return $this->extraAttributesDescriptions;
    }

    /**
     * @param array $extraAttributesFieldTypes
     */
    public function setExtraAttributesFieldTypes(array $extraAttributesFieldTypes)
    {
        $this->extraAttributesFieldTypes = $extraAttributesFieldTypes;
    }

    /**
     * @return array
     */
    public function getExtraAttributesFieldTypes()
    {
        return $this->extraAttributesFieldTypes;
    }

    /**
     * @param array $extraAttributesFieldParams
     */
    public function setExtraAttributesFieldParams(array $extraAttributesFieldParams)
    {
        $this->extraAttributesFieldParams = $extraAttributesFieldParams;
    }

    /**
     * @return array
     */
    public function getExtraAttributesFieldParams()
    {
        return $this->extraAttributesFieldParams;
    }

    /**
     * Get description of the given extra attribute.
     * @param string $extraAttribute
     * @return string|null
     */
    public function extraAttributeDescription($extraAttribute) {
        if (isset($this->extraAttributesDescriptions[$extraAttribute])) {
            return $this->extraAttributesDescriptions[$extraAttribute];
        }

        return null;
    }

    /**
     * Get form field type for the given extra attribute.
     * @param string $extraAttribute
     * @return string
     */
    public function extraAttributeFieldType($extraAttribute)
    {
        if (isset($this->extraAttributesFieldTypes[$extraAttribute])) {
            return $this->extraAttributesFieldTypes[$extraAttribute];
        }

        return 'text';
    }

    /**
     * Get form field params for the given extra attribute.
     *
     * @param string $extraAttribute
     * @param string $param
     * @return string|null
     */
    public function extraAttributeFieldParam($extraAttribute, $param) {
        if (isset($this->extraAttributesFieldParams[$extraAttribute])
                && isset($this->extraAttributesFieldParams[$extraAttribute][$param])) {
            return $this->extraAttributesFieldParams[$extraAttribute][$param];
        }

        return null;
    }

    /**
     * Get names of core attributes, i.e. attributes that are common for ALL methods.
     * @return array
     */
    public function coreAttributeNames()
    {
        return ['k', 'r', 'o'];
    }

    public function attributes() {
        return ArrayHelper::merge(
            $this->coreAttributeNames(),
            $this->getExtraAttributes()
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            [
                [['k', 'r'], 'required'],
            ],
            $this->getExtraRules()
        );
    }
}
