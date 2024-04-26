<?php

namespace App\Modules;

interface IModule {
    function getName();
    function getTitle();
    function getPresenterByName(string $name);
    function setPresenter(IPresenter $presenter);
    function getPresenters();
    function registerPresenter(IPresenter $presenter);
    function getNavbar();
}

?>