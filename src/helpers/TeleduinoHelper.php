<?php

namespace gogl92\teleduino\helpers;

use yii\base\Object;

/**
 * Class TeleduinoHelper various helper methods for formatting output of the data.
 *
 * @author Andriy Kmit' <dev@madand.net>
 */
class TeleduinoHelper extends Object
{
    /**
     * Format value in milliseconds bo be more human readable.
     *
     * @param integer $milliseconds
     * @return string human readable uptime value.
     */
    public static function formatUptime($milliseconds)
    {
        if ($milliseconds < 1000) {
            return "$milliseconds ms";
        }

        $parts   = [];
        $seconds = round($milliseconds / 1000, 3);

        if ($seconds > 60 * 60 * 24) {
            $days    = floor($seconds / 60 * 60 * 24);
            $parts[] = "{$days}d";
            $seconds -= $days * 60 * 60 * 24;
        }

        if ($seconds > 60 * 60) {
            $hours   = floor($seconds / 60 * 60);
            $parts[] = "{$hours}h";
            $seconds -= $hours * 60 * 60;
        }

        if ($seconds > 60) {
            $minutes = floor($seconds / 60);
            $parts[] = "{$minutes}m";
            $seconds -= $minutes * 60;
        }

        $parts[] = "{$seconds}s";

        return implode(' ', $parts);
    }


    /**
     * Filter validator for transforming comma separated string into array.
     * @param string $value comma separated string.
     * @return array
     */
    public static function commaSeparatedToArray($value)
    {
        return explode(',', $value);
    }
}
