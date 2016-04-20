<?php

namespace madand\teleduino\models;

use yii\base\Model;

/**
 * Class Response encapsulates data received from the server.
 *
 * @author Andriy Kmit' <dev@madand.net>
 */
class Response extends Model
{
    /**
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $message;

    /**
     * @var integer
     */
    private $result = 0;

    /**
     * @var string
     */
    private $time;

    /**
     * @var array
     */
    private $values = [];

    /**
     * Constructor.
     *
     * @param integer $status response status code
     * @param string|stdClass $body instance of JSON decoded object, or text if json_decode failed.
     */
    public function __construct($status, $body)
    {
        if (is_object($body)) {
            $this->status = $body->status;
            $this->message = $body->message;

            if (is_object($body->response)) {
                $this->result = $body->response->result;
                $this->values = $body->response->values;
                $this->time = $body->response->time;
            }
        } else {
            $this->status = (int)$status;
            $this->message = strip_tags($body);
        }
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Check whether the request was successful.
     * @return bool
     */
    public function isSuccessful() {
        return $this->result === 1;
    }

    public function getErrorMessage()
    {
        if (200 !== $this->status) {
            return "Error code: {$this->status}\n{$this->message}";
        }

        if (0 === $this->result) {
            return 'Server responded with "result" set to 0.';
        }

        return 'Unknown error.';
    }
}
