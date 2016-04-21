<?php

namespace gogl92\teleduino\console;

use gogl92\teleduino\commands\ApiController;
use gogl92\teleduino\components\Api;
use Yii;
use yii\base\Application;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\Console;
use yii\helpers\Inflector;

/**
 * Class HelpController
 *
 * @package gogl92\teleduino\commands
 * @author Andriy Kmit' <dev@madand.net>
 */
class HelpController extends \yii\console\controllers\HelpController
{
    public function getActions($controller)
    {
        // Fallback ot default implementation if not dealing with out module's commands.
        if (!($controller instanceof ApiController)) {
            return parent::getActions($controller);
        }

        $actions = array_keys($controller->actions());

        return array_unique($actions);
    }


    /**
     * Displays the detailed information of a command action.
     * @param Controller $controller the controller instance
     * @param string $actionID action ID
     * @throws Exception if the action does not exist
     */
    protected function getSubCommandHelp($controller, $actionID)
    {
        // Fallback ot default implementation if not dealing with out module's commands.
        if (!($controller instanceof ApiController)) {
            parent::getSubCommandHelp($controller, $actionID);

            return ;
        }

        $action = $controller->createAction($actionID);
        if ($action === null) {
            $name = $this->ansiFormat(rtrim($controller->getUniqueId() . '/' . $actionID, '/'), Console::FG_YELLOW);
            throw new Exception("No help for unknown sub-command \"$name\".");
        }

        $description = $controller->getActionHelp($action);
        if ($description !== '') {
            $this->stdout("\nDESCRIPTION\n", Console::BOLD);
            $this->stdout("\n$description\n\n");
        }

        $this->stdout("\nUSAGE\n\n", Console::BOLD);
        $scriptName = $this->getScriptName();
        if ($action->id === $controller->defaultAction) {
            $this->stdout($scriptName . ' ' . $this->ansiFormat($controller->getUniqueId(), Console::FG_YELLOW));
        } else {
            $this->stdout($scriptName . ' ' . $this->ansiFormat($action->getUniqueId(), Console::FG_YELLOW));
        }

        $args = $controller->getActionArgsHelp($action);
        foreach ($args as $name => $arg) {
            if ($arg['required']) {
                $this->stdout(' <' . $name . '>', Console::FG_CYAN);
            } else {
                $this->stdout(' [' . $name . ']', Console::FG_CYAN);
            }
        }

        $options = $controller->getActionOptionsHelp($action);

        if (!empty($options)) {
            $this->stdout(' [--optionName=optionValue ...]', Console::FG_RED);
        }
        $this->stdout("\n\n");

        if (!empty($args)) {
            foreach ($args as $name => $arg) {
                $this->stdout($this->formatOptionHelp(
                        '- ' . $this->ansiFormat($name, Console::FG_CYAN),
                        $arg['required'],
                        $arg['type'],
                        $arg['default'],
                        $arg['comment']) . "\n\n");
            }
        }

        if (!empty($options)) {
            $this->stdout("\nOPTIONS\n\n", Console::BOLD);
            foreach ($options as $name => $option) {
                $this->stdout($this->formatOptionHelp(
                        $this->ansiFormat('--' . $name, Console::FG_RED, empty($option['required']) ? Console::FG_RED : Console::BOLD),
                        !empty($option['required']),
                        $option['type'],
                        $option['default'],
                        $option['comment']) . "\n\n");
            }
        }

        $this->stdout($controller->getGlobalOptionsHelp());
    }
}
