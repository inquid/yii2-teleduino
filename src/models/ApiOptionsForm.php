<?php

namespace madand\teleduino\models;

use yii\base\Model;

/**
 * Class ApiOptionsForm
 *
 * @package models
 * @author Andriy Kmit' <dev@madand.net>
 */
class ApiOptionsForm extends Model
{
    public $api_endpoint;
    public $api_key;

    public function rules()
    {
        return [
            [['api_endpoint', 'api_key'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'api_endpoint'=>'API Endpoint (request URL)',
            'api_key'=>'API Key',
        ];
    }
}
