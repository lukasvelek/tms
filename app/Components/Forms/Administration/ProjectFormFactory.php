<?php

namespace App\Components\Forms;

use App\Constants\ProjectStatus;
use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Repositories\ClientRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\UserRepository;

class ProjectFormFactory extends AFormFactory {
    private ProjectRepository $projectRepository;
    private UserRepository $userRepository;
    private ClientRepository $clientRepository;

    private ?int $idProject;

    public function __construct(Database $db, Logger $logger, ProjectRepository $projectRepository, UserRepository $userRepository, ClientRepository $clientRepository, string $formHandlerUrl = '', ?int $idProject = null) {
        parent::__construct($db, $logger, $formHandlerUrl);

        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->clientRepository = $clientRepository;

        $this->idProject = $idProject;

        $this->createForm();
    }

    private function createForm() {
        $projectName = '';
        $idProjectManager = '';
        $idClient = '';
        $status = '';

        if($this->idProject !== NULL) {
            $project = $this->projectRepository->getProjectById($this->idProject);

            $projectName = $project->getName();
            $idProjectManager = $project->getIdProjectManager();
            $idClient = $project->getIdClient();
            $status = $project->getStatus();
        }

        $users = $this->userRepository->getAllUsers();

        $projectManagers = [];
        foreach($users as $user) {
            $tmp = [
                'text' => $user->getFullname(),
                'value' => $user->getId()
            ];

            if($idProjectManager == $user->getId()) {
                $tmp['selected'] = 'selected';
            }

            $projectManagers[] = $tmp;
        }

        $clients = $this->clientRepository->getAllClients();

        $clientArr = [];
        foreach($clients as $client) {
            $tmp = [
                'text' => $client->getName(),
                'value' => $client->getId()
            ];

            if($idClient == $client->getId()) {
                $tmp['selected'] = 'selected';
            }

            $clientArr[] = $tmp;
        }

        $statusDb = ProjectStatus::getAll();

        $statusArr = [];
        foreach($statusDb as $k => $v) {
            $tmp = [
                'text' => $v,
                'value' => $k
            ];

            if($status == $k) {
                $tmp['selected'] = 'selected';
            }

            $statusArr[] = $tmp;
        }

        $this->fb   ->setMethod('POST')->setAction($this->formHandlerUrl)
                    ->addLabel('Project name', 'project_name', true)
                    ->addText('project_name', $projectName, '', true)

                    ->addLabel('Project manager', 'project_manager', true)
                    ->addElement($this->fb->createSelect()->setName('project_manager')->addOptionsBasedOnArray($projectManagers))

                    ->addLabel('Client', 'client', true)
                    ->addElement($this->fb->createSelect()->setName('client')->addOptionsBasedOnArray($clientArr))

                    ->addLabel('Status', 'status', true)
                    ->addElement($this->fb->createSelect()->setName('status')->addOptionsBasedOnArray($statusArr))

                    ->addElement($this->fb->createSubmit(($this->idProject === NULL) ? 'Create' : 'Save'));
        ;
    }
}

?>