<?php
/**
 * This file contains configuration data about all Teleduino API methods.
 * It is required by the class {@link \madand\teleduino\components\Api}.
 *
 * @author Andriy Kmit' <dev@madand.net>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

use madand\teleduino\helpers\TeleduinoHelper;

/**
 * [
 *     'Group name' => [
 *         'API Method' => [ *method configuration...* ]
 *     ],
 * ]
 */
return [
    'EEPROM'          => [
        'resetEeprom' => [
            'description' => 'Resets the EEPROM.',
        ],
        'setEeprom'   => [
            'description'   => 'Write data to the EEPROM.',
            'requestParams' => [
                'offset' => [
                    'description' => 'Starting position of the EEPROM to write to.',
                    'validators'  => [
                        ['offset', 'required'],
                        ['offset', 'integer', 'min' => 0],
                    ],
                ],
                'bytes'  => [
                    'description' => 'Text to write to the EEPROM. Maximum length is 253 bytes.',
                    'fieldType'=>'textarea',
                    'validators'  => [
                        ['bytes', 'required'],
                        ['bytes', 'string', 'max' => 253],
                    ],
                ],
            ],
        ],
        'getEeprom'   => [
            'description'              => 'Read data from the EEPROM.',
            'requestParams'            => [
                'offset'     => [
                    'description' => 'Starting position of the EEPROM to read from.',
                    'validators'  => [
                        ['offset', 'required'],
                        ['offset', 'integer', 'min' => 0],
                    ],
                ],
                'byte_count' => [
                    'description' => 'Number of bytes to read from the EEPROM. Possible values are 0 - 255.',
                    'validators'  => [
                        ['byte_count', 'required'],
                        ['byte_count', 'integer', 'min' => 0, 'max' => 255],
                    ],
                ],
            ],
            'formatResponseValues'     => function ($values) {
                    return "Plain text: \"{$values[0]}\"" . PHP_EOL
                    . 'Hex-encoded: "' . bin2hex($values[0]) . '"';
                },
            'formatResponseValuesHtml' => function ($values) {
                    return "Plain text:<br/><code>{$values[0]}</code><br/>Hex-encoded:<br/><code>"
                    . bin2hex($values[0]) . "</code>";
                },
        ],
    ],
    'I/O'             => [
        'definePinMode'    => [
            'description'   => 'Sets the digital mode for a pin.',
            'requestParams' => [
                'pin'  => [
                    'description' => 'Digital pin. Possible values are 0 - 19.',
                    'validators'  => [
                        ['pin', 'required'],
                        ['pin', 'integer', 'min' => 0, 'max' => 19],
                    ],
                ],
                'mode' => [
                    'description' => 'Mode. Possible values are 0 (input) or 1 (output).',
                    'validators'  => [
                        ['mode', 'required'],
                        ['mode', 'in', 'range' => [0, 1], 'message'=>'{attribute} must be either 0 or 1.'],
                    ],
                ],
            ],
        ],
        'setDigitalOutput' => [
            'description'   => 'Sets the output on a digital pin.',
            'requestParams' => [
                'pin'         => [
                    'description' => 'Digital pin. Possible values are 0 - 19.',
                    'validators'  => [
                        ['pin', 'required'],
                        ['pin', 'integer', 'min' => 0, 'max' => 19],
                    ],
                ],
                'output'      => [
                    'description' => 'Output. Possible values are 0 (low), 1 (high) or 2 (toggle).',
                    'validators'  => [
                        ['output', 'required'],
                        ['output', 'in', 'range' => [0, 1, 2], 'message'=>'{attribute} must be either 0, 1 or 2.'],
                    ],
                ],
                'expire_time' => [
                    'description' => 'Optional expire time (in milliseconds) for output. 0 means no expiry. Possible values are 0 - 16777215.',
                    'validators'  => [
                        ['expire_time', 'integer', 'min' => 0, 'max' => 16777215],
                    ],
                ],
                'save'        => [
                    'description' => 'Optionally save the output as a preset (328-0.6.9 and above). Can only be set if expire_time is 0. Possible values are 0 (don\'t save) or 1 (save).',
                    'validators'  => [
                        ['save', 'in', 'range' => [0, 1], 'message'=>'{attribute} must be either 0 or 1.'],
                    ],
                ],
            ],
        ],
        'setPwmOutput'     => [
            'description'   => 'Sets the duty cycle of a PWM pin.',
            'requestParams' => [
                'pin'    => [
                    'description' => 'PWM pin. Possible values are 3, 5 - 7, 9 - 11.',
                    'validators'  => [
                        ['pin', 'required'],
                        ['pin', 'in', 'range' => [3, 5, 6, 7, 9, 10, 11], 'message'=>'{attribute} must be either 3, 5, 6, 7, 9, 10 or 11.'],
                    ],
                ],
                'output' => [
                    'description' => 'Output. Possible values are 0 (low) - 255 (high).',
                    'validators'  => [
                        ['output', 'required'],
                        ['output', 'integer', 'min' => 0, 'max' => 255],
                    ],
                ],
            ],
        ],
        'getDigitalInput' => [
            'description'   => 'Returns the input state of a digital pin.',
            'requestParams' => [
                'pin'         => [
                    'description' => 'Digital pin. Possible values are 0 - 19.',
                    'validators'  => [
                        ['pin', 'required'],
                        ['pin', 'integer', 'min' => 0, 'max' => 19],
                    ],
                ],
            ],
            'formatResponseValues' => function ($values) {
                    return "Input state of digital pin: {$values[0]}";
                },
        ],
        'getAnalogInput' => [
            'description'   => 'Returns the input value of an analog pin.',
            'requestParams' => [
                'pin'         => [
                    'description' => 'Analog pin. Possible values are 14 - 21.',
                    'validators'  => [
                        ['pin', 'required'],
                        ['pin', 'integer', 'min' => 14, 'max' => 21],
                    ],
                ],
            ],
            'formatResponseValues' => function ($values) {
                    return "Input value of analog pin: {$values[0]}";
                },
        ],
        'getAllInputs' => [
            'description'   => 'Returns the input values of all the digital and analog pins.',
            'formatResponseValues' => function ($values) {
                    $output = "Input values of pins:" . PHP_EOL;
                    $output .= sprintf('%3s: %5s', 'PIN', 'VALUE') . PHP_EOL;
                    foreach ($values as $i => $val) {
                        $output .= sprintf('%3d: %5d', $i + 1, $val) . PHP_EOL;
                    }

                    return $output;
                },
            'formatResponseValuesHtml' => function ($values) {
                    $output = "Input values of pins:<br/><pre>";
                    $output .= sprintf('%3s: %5s', 'PIN', 'VALUE') . "<br/>";
                    foreach ($values as $i => $val) {
                        $output .= sprintf('%3d: %5d', $i + 1, $val) . "<br/>";
                    }

                    return $output . '</pre>';
                },
        ],
        'setDigitalOutputs' => [
            'description'   => 'Sets the outputs of one or more digital pins.',
            'requestParams' => [
                'outputs'         => [
                    'description' => 'An array of outputs. Possible values are 0 (low), 1 (high) or 2 (toggle).
Values should be separated by comma.
Example: "1,0,0,2,0".',
                    'validators'  => [
                        ['outputs', 'required'],
                        ['outputs', 'match', 'pattern' => '/^(?:0|1|2)(?:,(?:0|1|2))*$/', 'message' => 'Please provide comma separated values in range 0-2.'],
                        ['outputs', 'filter', 'filter'=>[TeleduinoHelper::className(), 'commaSeparatedToArray']],
                    ],
                ],
                'expire_times'         => [
                    'description' => 'Optional array of expire times (in milliseconds) for output. 0 means no expiry. Possible values are 0 - 16777215.
Values should be separated by comma.
Example: "1000,0,500,200,0".',
                    'validators'  => [
                        ['expire_times', 'match', 'pattern' => '/^\d(?:,\d)*$/', 'message' => 'Please provide comma separated values in range 0-16777215.'],
                        ['expire_times', 'filter', 'filter'=>[TeleduinoHelper::className(), 'commaSeparatedToArray']],
                    ],
                ],
                'offset' => [
                    'description' => 'Optional array offset. Defaults to 0.
When 0: output[0] = pin 0, output[1] = pin 1
When 5: output[0] = pin 5, output[1] = pin 6
etc ',
                    'validators'  => [
                        ['offset', 'integer', 'min' => 0],
                    ],
                ],
            ],
        ],
    ],
    'Serial'          => [
        'defineSerial'     => [
            'description'   => 'Defines a serial port for use.',
            'requestParams' => [
                'port'    => [
                    'description' => 'The port we are defining. The only possible value is 0.',
                    'fieldType'=>'hidden',
                    'validators'  => [
                        ['port', 'default', 'value' => '0'],
                        ['port', 'required', 'requiredValue' => '0'],
                    ],
                ],
                'baud' => [
                    'description' => 'The baud rate of the port.',
                    'fieldType'=>'select',
                    'fieldParams'=>[
                        'selectOptions' => [
                            300    => 300,
                            1200   => 1200,
                            2400   => 2400,
                            4800   => 4800,
                            9600   => 9600,
                            14400  => 14400,
                            19200  => 19200,
                            28800  => 28800,
                            38400  => 38400,
                            57600  => 57600,
                            115200 => 115200,
                        ],
                    ],
                    'validators'  => [
                        ['baud', 'required'],
                        [
                            'baud',
                            'in',
                            'range' => [300, 1200, 2400, 4800, 9600, 14400, 19200, 28800, 38400, 57600, 115200],
                            'message'=>'{attribute} must be either 300, 1200, 2400, 4800, 9600, 14400, 19200, 28800, 38400, 57600 or 115200.'
                        ],
                    ],
                ],
            ],
        ],
        'setSerial'     => [
            'description'   => 'Write data to the serial port.',
            'requestParams' => [
                'port'    => [
                    'description' => 'The port we are defining. The only possible value is 0.',
                    'fieldType'=>'hidden',
                    'validators'  => [
                        ['port', 'default', 'value' => '0'],
                        ['port', 'required', 'requiredValue' => '0'],
                    ],
                ],
                'bytes'  => [
                    'description' => 'Text to send to the serial port. Maximum length is 254 bytes.',
                    'fieldType'=>'textarea',
                    'validators'  => [
                        ['bytes', 'required'],
                        ['bytes', 'string', 'max' => 254],
                    ],
                ],
            ],
        ],
        'getSerial'     => [
            'description'   => 'Read data from the serial port.',
            'requestParams' => [
                'port'    => [
                    'description' => 'The port we are reading from. The only possible value is 0.',
                    'fieldType'=>'hidden',
                    'validators'  => [
                        ['port', 'default', 'value' => '0'],
                        ['port', 'required', 'requiredValue' => '0'],
                    ],
                ],
                'byte_count' => [
                    'description' => 'Maximum number of bytes to read from the serial port. Possible values are 0 - 255.',
                    'validators'  => [
                        ['byte_count', 'required'],
                        ['byte_count', 'integer', 'min' => 0, 'max' => 255],
                    ],
                ],
            ],
            'formatResponseValues'     => function ($values) {
                    return "Plain text: \"{$values[0]}\"" . PHP_EOL
                    . 'Hex-encoded: "' . bin2hex($values[0]) . '"';
                },
            'formatResponseValuesHtml' => function ($values) {
                    return "Plain text:<br/><code>{$values[0]}</code><br/>Hex-encoded:<br/><code>"
                    . bin2hex($values[0]) . "</code>";
                },
        ],
        'flushSerial'     => [
            'description'   => 'Flushes the serial port buffer.',
            'requestParams' => [
                'port'    => [
                    'description' => 'The port we are defining. The only possible value is 0.',
                    'fieldType'=>'hidden',
                    'validators'  => [
                        ['port', 'default', 'value' => '0'],
                        ['port', 'required', 'requiredValue' => '0'],
                    ],
                ],
            ],
        ],
    ],
    'Servo'           => [
        'defineServo'     => [
            'description'   => 'Defines the instance of a servo.',
            'requestParams' => [
                'servo' => [
                    'description' => 'Servo instance. Possible values are 0 - 5.',
                    'validators'  => [
                        ['servo', 'required'],
                        ['servo', 'integer', 'min' => 0, 'max' => 5],
                    ],
                ],
                'pin' => [
                    'description' => 'Digital pin. Possible values are 0 - 21.',
                    'validators'  => [
                        ['pin', 'required'],
                        ['pin', 'integer', 'min' => 0, 'max' => 21],
                    ],
                ],
            ],
        ],
        'setServo'     => [
            'description'   => 'Sets the position of a servo.',
            'requestParams' => [
                'servo' => [
                    'description' => 'Servo instance. Possible values are 0 - 5.',
                    'validators'  => [
                        ['servo', 'required'],
                        ['servo', 'integer', 'min' => 0, 'max' => 5],
                    ],
                ],
                'position' => [
                    'description' => 'Position to set the servo to. Possible values are 0 - 180.',
                    'validators'  => [
                        ['position', 'required'],
                        ['position', 'integer', 'min' => 0, 'max' => 180],
                    ],
                ],
            ],
        ],
    ],
    'Shift Registers' => [
        'defineShiftRegister'     => [
            'description'   => 'Defines a cascade of up to 32 shift registers.',
            'requestParams' => [
                'shift_register' => [
                    'description' => 'The shift register cascade we are defining. Possible values are 0 or 1.',
                    'validators'  => [
                        ['shift_register', 'required'],
                        ['shift_register', 'in', 'range'=>[0,1], 'message'=>'{attribute} must be either 0 or 1.'],
                    ],
                ],
                'clock_pin' => [
                    'description' => 'The digital pin which is connected to the shift register clock pin. Possible values are 0 - 21.',
                    'validators'  => [
                        ['clock_pin', 'required'],
                        ['clock_pin', 'integer', 'min' => 0, 'max' => 21],
                    ],
                ],
                'data_pin' => [
                    'description' => 'The digital pin which is connected to the shift register data pin. Possible values are 0 - 21.',
                    'validators'  => [
                        ['data_pin', 'required'],
                        ['data_pin', 'integer', 'min' => 0, 'max' => 21],
                    ],
                ],
                'latch_pin' => [
                    'description' => 'The digital pin which is connected to the shift register latch pin. Possible values are 0 - 21.',
                    'validators'  => [
                        ['latch_pin', 'required'],
                        ['latch_pin', 'integer', 'min' => 0, 'max' => 21],
                    ],
                ],
                'enable_pin' => [
                    'description' => 'Optional. The digital pin which is used to enable the shift register. Possible values are 0 - 21.',
                    'validators'  => [
                        ['enable_pin', 'integer', 'min' => 0, 'max' => 21],
                    ],
                ],
            ],
        ],
        'setShiftRegister'     => [
            'description'   => 'Sets the output values for a shift register cascade.',
            'requestParams' => [
                'shift_register' => [
                    'description' => 'The shift register cascade we are setting. Possible values are 0 or 1.',
                    'validators'  => [
                        ['shift_register', 'required'],
                        ['shift_register', 'in', 'range'=>[0,1], 'message'=>'{attribute} must be either 0 or 1.'],
                    ],
                ],
                'outputs'         => [
                    'description' => ' 	An array of integers. Each element of the array represents a shift register (up to 32).
Possible values per element are 0 - 255.
Values should be separated by comma.
Example: "100,0,50,20,0".',
                    'validators'  => [
                        ['outputs', 'required'],
                        ['outputs', 'match', 'pattern' => '/^\d(?:,\d){,31}$/', 'message' => 'Please provide comma separated values in range 0-255.'],
                        ['outputs', 'filter', 'filter'=>[TeleduinoHelper::className(), 'commaSeparatedToArray']],
                    ],
                ],
            ],
        ],
        'mergeShiftRegister'     => [
            'description'   => 'Merges new output values with any existing values.',
            'requestParams' => [
                'shift_register' => [
                    'description' => 'The shift register cascade we are setting. Possible values are 0 or 1.',
                    'validators'  => [
                        ['shift_register', 'required'],
                        ['shift_register', 'in', 'range'=>[0,1], 'message'=>'{attribute} must be either 0 or 1.'],
                    ],
                ],
                'action' => [
                    'description' => 'Action to apply to affected outputs. Possible values are 0 (low], 1 (high) or 2 (toggle).',
                    'validators'  => [
                        ['action', 'required'],
                        ['action', 'in', 'range'=>[0,1,2], 'message'=>'{attribute} must be either 0, 1 or 2.'],
                    ],
                ],
                'expire_time' => [
                    'description' => 'Expire time in milliseconds. Once expired the action reverses itself. Set to 0 to never expire. Possible values are 0 - 16777215.',
                    'validators'  => [
                        ['expire_time', 'required'],
                        ['expire_time', 'integer', 'min' => 0, 'max' => 16777215],
                    ],
                ],
                'outputs'         => [
                    'description' => 'An array of integers. Each element of the array represents a shift register (up to 32).
Possible values per element are 0 - 255.
Values should be separated by comma.
Example: "100,0,50,20,0".',
                    'validators'  => [
                        ['outputs', 'required'],
                        ['outputs', 'match', 'pattern' => '/^\d(?:,\d){,31}$/', 'message' => 'Please provide comma separated values in range 0-255.'],
                        ['outputs', 'filter', 'filter'=>[TeleduinoHelper::className(), 'commaSeparatedToArray']],
                    ],
                ],
            ],
        ],
        'getShiftRegister'     => [
            'description'   => 'Returns the output values for a shift register cascade.',
            'requestParams' => [
                'shift_register' => [
                    'description' => 'The shift register cascade we are getting. Possible values are 0 or 1.',
                    'validators'  => [
                        ['shift_register', 'required'],
                        ['shift_register', 'in', 'range'=>[0,1], 'message'=>'{attribute} must be either 0 or 1.'],
                    ],
                ],
            ],
            'formatResponseValues' => function ($values) {
                    $output = "Output values of shift registers:" . PHP_EOL;
                    $output .= sprintf('%3s: %5s', 'REG', 'VALUE') . PHP_EOL;
                    foreach ($values as $i => $val) {
                        $output .= sprintf('%3d: %5d', $i + 1, $val) . PHP_EOL;
                    }

                    return $output;
                },
            'formatResponseValuesHtml' => function ($values) {
                    $output = "Output values of shift registers:<br/><pre>";
                    $output .= sprintf('%3s: %5s', 'REG', 'VALUE') . "<br/>";
                    foreach ($values as $i => $val) {
                        $output .= sprintf('%3d: %5d', $i + 1, $val) . "<br/>";
                    }

                    return $output . '</pre>';
                },
        ],
    ],
    'System'          => [
        'reset'           => [
            'description' => 'Resets the device.',
        ],
        'getVersion'      => [
            'description'          => 'Returns the firmware version of the device.',
            'formatResponseValues' => function ($values) {
                    return "Firmware version: {$values[0]}";
                },
        ],
        'setStatusLedPin' => [
            'description'   => 'Sets the digital pin to be used for the status LED.',
            'requestParams' => [
                'pin' => [
                    'description' => 'Digital pin. Possible values are 0 - 21.',
                    'validators'  => [
                        ['pin', 'required'],
                        ['pin', 'integer', 'min' => 0, 'max' => 21],
                    ],
                ],
            ],
        ],
        'setStatusLed'    => [
            'description'   => 'Sends a flash sequence to the status LED.',
            'requestParams' => [
                'count' => [
                    'description' => 'Flash count to be sequenced. If flash sequence time exceeds 5 seconds then timeout may occur. Possible values are 0 - 255.',
                    'validators'  => [
                        ['count', 'required'],
                        ['count', 'integer', 'min' => 0, 'max' => 255],
                    ],
                ],
            ],
        ],
        'getFreeMemory'   => [
            'description'          => 'Returns the amount of free memory available on the device.',
            'formatResponseValues' => function ($values) {
                    return "Free memory: {$values[0]} bytes.";
                }
        ],
        'ping'            => [
            'description' => 'Pings the device, resulting in a quick flash of the status LED.',
        ],
        'getUptime'       => [
            'description'          => 'Returns the uptime of the device.',
            'formatResponseValues' => function ($values) {
                    return 'Uptime: ' . TeleduinoHelper::formatUptime($values[0]) . '.';
                }
        ],
        'loadPresets'     => [
            'description' => 'Loads preset values from EEPROM. This is the same as what occurs when the device starts.',
        ],
    ],
    'Wire (TWI/I2C)'  => [
        'defineWire'     => [
            'description' => 'Starts the Wire interface.',
        ],
        'setWire'   => [
            'description'   => 'Write data to the Wire (TWI/I2C) bus.',
            'requestParams' => [
                'address' => [
                    'description' => 'The 7 bit address of the device to write to. Possible values are 1 - 127.',
                    'validators'  => [
                        ['address', 'required'],
                        ['address', 'integer', 'min' => 1, 'max'=>127],
                    ],
                ],
                'bytes'  => [
                    'description' => 'Text to write to the Wire (TWI/I2C) bus. Maximum length is 32 bytes.',
                    'validators'  => [
                        ['bytes', 'required'],
                        ['bytes', 'string', 'max' => 32],
                    ],
                ],
            ],
            'formatResponseValues' => function ($values) {
                    $output = "The response from the Wire (TWI/I2C) bus:" . PHP_EOL;

                    switch ($values[0]) {
                        case 0:
                            $output .= "0 - success";
                            break;
                        case 1:
                            $output .= "1 - data too long to fit in transmit buffer";
                            break;
                        case 2:
                            $output .= "2 - received NACK on transmit of address";
                            break;
                        case 3:
                            $output .= "3 - received NACK on transmit of data";
                            break;
                        case 4:
                            $output .= "4 - other error";
                            break;
                    }

                    return $output;
                },
            'formatResponseValuesHtml' => function ($values) {
                    $output = "The response from the Wire (TWI/I2C) bus:<br/>";

                    switch ($values[0]) {
                        case 0:
                            $output .= "0 - success";
                            break;
                        case 1:
                            $output .= "1 - data too long to fit in transmit buffer";
                            break;
                        case 2:
                            $output .= "2 - received NACK on transmit of address";
                            break;
                        case 3:
                            $output .= "3 - received NACK on transmit of data";
                            break;
                        case 4:
                            $output .= "4 - other error";
                            break;
                    }

                    return $output;
                },
        ],
        'getWire'   => [
            'description'   => 'Request data from the Wire (TWI/I2C) bus.',
            'requestParams' => [
                'address' => [
                    'description' => 'The 7 bit address of the device to write to. Possible values are 1 - 127.',
                    'validators'  => [
                        ['address', 'required'],
                        ['address', 'integer', 'min' => 1, 'max'=>127],
                    ],
                ],
                'byte_count' => [
                    'description' => 'Number of bytes being requested. Possible values are 1 - 32.',
                    'validators'  => [
                        ['byte_count', 'required'],
                        ['byte_count', 'integer', 'min' => 1, 'max'=>32],
                    ],
                ],
            ],
            'formatResponseValues'     => function ($values) {
                    return "Plain text: \"{$values[0]}\"" . PHP_EOL
                    . 'Hex-encoded: "' . bin2hex($values[0]) . '"';
                },
            'formatResponseValuesHtml' => function ($values) {
                    return "Plain text:<br/><code>{$values[0]}</code><br/>Hex-encoded:<br/><code>"
                    . bin2hex($values[0]) . "</code>";
                },
        ],
    ],
];
