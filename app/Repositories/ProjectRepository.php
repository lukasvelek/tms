<?php

namespace App\Repositories;

use App\Constants\CacheCategories;
use App\Core\CacheManager;
use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Entities\ProjectEntity;

class ProjectRepository extends ARepository {
    private CacheManager $cm;

    public function __construct(Database $db, Logger $logger) {
        parent::__construct($db, $logger);

        $this->cm = CacheManager::getTemporaryObject(CacheCategories::PROJECTS);
    }

    public function getProjectById(int $id) {
        return $this->cm->load($id, function() use ($id) {
            $qb = $this->qb(__METHOD__);

            $qb ->select(['*'])
                ->from('projects')
                ->where('id = ?', [$id])
                ->execute();

            $project = null;
            while($row = $qb->fetchAssoc()) {
                $project = ProjectEntity::createProjectEntityFromDbRow($row);
            }

            return $project;
        });
    }

    public function getAllProjects() {
        $qb = $this->qb(__METHOD__);

        $qb ->select(['*'])
            ->from('projects')
            ->execute();

        $projects = [];
        while($row = $qb->fetchAssoc()) {
            $projects[] = ProjectEntity::createProjectEntityFromDbRow($row);
        }

        return $projects;
    }

    public function composeQueryForGrid(?string $method = null) {
        $qb = $this->qb($method ?? __METHOD__);

        $qb ->select(['*'])
            ->from('projects');

        return $qb;
    }

    public function getProjectCount() {
        $qb = $this->qb(__METHOD__);

        $qb ->select(['COUNT(id) AS cnt'])
            ->from('projects')
            ->execute();

        $count = 0;

        while($row = $qb->fetchAssoc()) {
            $count = $row['cnt'];
        }

        return $count;
    }
}

?>