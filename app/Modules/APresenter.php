<?php

namespace App\Modules;

use App\Core\FileManager;
use App\Core\TemplateManager;
use ReflectionMethod;

abstract class APresenter implements IPresenter {
    private string $name;
    private string $title;
    private string $templateText;
    private array $actions;
    private array $beforeRenderCallbacks;
    private IModule $module;
    
    protected TemplateManager $templateManager;
    protected ?object $template;
    
    public bool $allowWhenLoginProcess;
    public string $subpanel = '';
    public bool $drawSubpanel = false;

    protected function __construct(string $name, string $title = '', bool $allowWhenLoginProcess = false) {
        $this->name = $name;
        $this->allowWhenLoginProcess = $allowWhenLoginProcess;
        $this->templateText = '';
        $this->template = null;

        if($title == '') {
            $this->title = $this->name;
        } else {
            $this->title = $title;
        }

        $this->templateManager = TemplateManager::getTemporaryObject();

        $this->actions = [];
        $this->beforeRenderCallbacks = [];
    }

    protected function fill(array $data) {
        $this->templateManager->fill($data, $this->templateText);
    }

    protected function loadTemplate(string $path) {
        return $this->templateManager->loadTemplate($path);
    }

    public function getActions() {
        return $this->actions;
    }

    public function getModule() {
        return $this->module;
    }

    public function setModule(IModule $module) {
        $this->module = $module;
    }

    public function getName() {
        return $this->name;
    }

    public function getTitle() {
        return $this->title;
    }

    public function performAction(string $name) {
        // action
        // render

        $capitalized = ucfirst($name);
        $actionName = 'handle' . $capitalized;
        $renderName = 'render' . $capitalized;

        if(method_exists($this, $actionName) && !method_exists($this, $renderName)) {
            $args = $this->fireMethodArgs($actionName);
            return $this->$actionName(...$args);
        } else if(method_exists($this, $actionName) && method_exists($this, $renderName)) {
            $actionArgs = $this->fireMethodArgs($actionName);
            $this->$actionName(...$actionArgs);
            $this->beforeRender($name);
            $renderArgs = $this->fireMethodArgs($renderName);
            $this->$renderName(...$renderArgs);
            return $this->afterRender($name);
        } else if(!method_exists($this, $actionName) && method_exists($this, $renderName)) {
            $this->beforeRender($name);
            $renderArgs = $this->fireMethodArgs($renderName);
            $this->$renderName(...$renderArgs);
            return $this->afterRender($name);
        } else {
            die('Methods ' . $actionName . ' or ' . $renderName . ' do not exist!');
        }
    }

    protected function beforeRender(string $name) {
        global $app;

        $app->logger->info('[RENDER START]', $this->module->getName() . ':' . $this->name . ':' . $name);

        // load template

        $file = __DIR__ . '\\' . $this->module->getName() . '\\Presenters\\templates\\' . $this->name . '\\' . $name . '.html';

        if(!FileManager::fileExists($file)) {
            $app->logger->info('[RENDER END]', $this->module->getName() . ':' . $this->name . ':' . $name);
            die('Template \'' . $file . '\' does not exist!');
        }

        $this->templateText = $this->loadTemplate($file);

        $this->template = new class() {
            private array $_internalValues;

            public function __construct() {
                $this->_internalValues = [];
            }

            public function __set(string $name, mixed $value) {
                $this->_internalValues[] = $name;
                $this->{$name} = $value;
            }

            public function __get(string $name) {
                return $this->{$name};
            }

            public function getToFill() {
                $array = [];

                foreach($this->_internalValues as $iv) {
                    $name = '$' . strtoupper($iv) . '$';
                    $value = $this->{$iv};

                    $array[$name] = $value;
                }

                return $array;
            }
        };

        if(!empty($this->beforeRenderCallbacks)) {
            foreach($this->beforeRenderCallbacks as $callback) {
                $callback($this->template);
            }
        }
    }

    protected function afterRender(string $name) {
        global $app;
        
        $data = $this->template->getToFill();
        $this->fill($data, $this->templateText);
        
        $app->logger->info('[RENDER END]', $this->module->getName() . ':' . $this->name . ':' . $name);
        
        return $this->templateText;
    }

    protected function setActions(array $actions) {
        $this->actions = $actions;
    }

    protected function getActionNamesFromClass(object $class, bool $save = true) {
        $methods = get_class_methods($class);

        $tempMethods = [];
        foreach($methods as $method) {
            if(str_contains($method, 'show')) {
                $tempMethods[$method] = substr($method, 4);
            }
        }

        if($save) {
            $this->actions = $tempMethods;
            return null;
        } else {
            return $tempMethods;
        }
    }

    protected function addBeforeRenderCallback(callable $callback) {
        $this->beforeRenderCallbacks[] = $callback;
    }

    private function fireMethodArgs(string $methodName) {
        $args = [];

        $reflection = new ReflectionMethod($this, $methodName);

        foreach($reflection->getParameters() as $param) {
            if(isset($_GET[$param->name])) {
                $args[$param->name] = $_GET[$param->name];
            } else if(isset($_POST[$param->name])) {
                $args[$param->name] = $_POST[$param->name];
            } else {
                $args[$param->name] = null;
            }
        }

        return $args;
    }
}

?>