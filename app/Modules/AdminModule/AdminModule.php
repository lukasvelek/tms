<?php

namespace App\Modules\AdminModule;

use App\Modules\IModule;
use App\Modules\IPresenter;

class AdminModule implements IModule {
    public IPresenter $currentPresenter;
    private string $name;
    private string $title;
    private array $presenters;

    public function __construct() {
        $this->name = 'AdminModule';
        $this->title = 'Test module';
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
}

?>