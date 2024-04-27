<?php

namespace App\Modules;

use App\Core\TemplateManager;

abstract class AModule implements IModule {
    public IPresenter $currentPresenter;
    public string $navbar;

    protected string $name;
    protected string $title;
    protected array $presenters;
    protected TemplateManager $templateManager;

    protected function __construct(string $name, string $title = '') {
        $this->name = $name;

        if($title == '') {
            $this->title = $title;
        }

        $this->templateManager = TemplateManager::getTemporaryObject();

        $this->navbar = $this->templateManager->loadTemplate(__DIR__ . '/' . $this->name . '/Presenters/templates/@layout/navbar.html');

        $this->checkAppUser();
    }

    public function getName() {
        return $this->name;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getPresenterByName(string $name) {
        if(array_key_exists($name, $this->presenters)) {
            return $this->presenters[$name];
        } else {
            return null;
        }
    }

    public function setPresenter(IPresenter $presenter) {
        $this->currentPresenter = $presenter;
    }

    public function registerPresenter(IPresenter $presenter) {
        $this->presenters[$presenter->getName()] = $presenter;
    }

    public function getPresenters() {
        return $this->presenters;
    }

    public function getNavbar() {
        return $this->navbar;
    }

    protected function fillNavbar(string $key, string|array $values, bool $place_space_between_elements_in_values_array = false) {
        if(is_array($values)) {
            $tmp = '';
            $i = 0;
            foreach($values as $value) {
                if(($i + 1) == count($values)) {
                    $tmp .= $value;
                } else {
                    if($place_space_between_elements_in_values_array === TRUE) {
                        $tmp .= $value . '&nbsp;&nbsp;';
                    } else {
                        $tmp .= $value;
                    }
                }

                $i++;
            }
            $values = $tmp;
        }

        $this->templateManager->fill([$key => $values], $this->navbar);
    }

    private function checkAppUser() {
        global $app;

        if($app->user === NULL && isset($_SESSION['id_current_user'])) {
            $user = $app->userRepository->getUserById($_SESSION['id_current_user']);
            $app->setCurrentUser($user);
        }
    }
}

?>