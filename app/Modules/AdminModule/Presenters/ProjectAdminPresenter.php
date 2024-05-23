<?php

namespace App\Modules\AdminModule;

use App\Components\Forms\ProjectFormFactory;
use App\Constants\FlashMessageTypes;
use App\UI\LinkBuilder;
use Exception;

class ProjectAdminPresenter extends AAdminPresenter {
    public function __construct() {
        parent::__construct('ProjectAdminPresenter', 'Project administration');

        $this->createSubList();
    }

    public function renderList(?int $gridPage) {
        if($gridPage === NULL) {
            $gridPage = 0;
        }
        $this->template->scripts = ['<script type="text/javascript">projectGridPaginator(' . $gridPage . ');</script>'];
        $this->template->links = [];
        $this->template->links[] = LinkBuilder::createAdvLink(['page' => 'ProjectAdmin:form'], 'New project');
    }

    public function renderForm() {
        global $app;

        $idProject = $this->httpGet('idProject');

        $projectFormFactory = new ProjectFormFactory($app->getConn(), $app->logger, $app->projectRepository, $app->userRepository, $app->clientRepository, '?page=AdminModule:ProjectAdmin:form&isSubmit=1', $idProject);

        $this->template->links = [];
        $this->template->links[] = LinkBuilder::createAdvLink(['page' => 'ProjectAdmin:list'], '&larr; Back');
        $this->template->form = $projectFormFactory->createComponent();
    }

    public function handleForm() {
        global $app;

        if($this->httpGet('isSubmit') !== NULL && $this->httpGet('isSubmit') == '1') {
            // process form
            $projectName = $this->httpPost('project_name');
            $idProjectManager = $this->httpPost('project_manager');
            $client = $this->httpPost('client');
            $status = $this->httpPost('status');

            $result = $app->projectRepository->createProject($projectName, $idProjectManager, $status, $client);

            if($result === NULL) {
                $app->flashMessage('Project \'' . $projectName . '\' created.', FlashMessageTypes::SUCCESS);
                $app->redirect('list');
            } else {
                throw new Exception($result);
            }
        }
    }
}

?>