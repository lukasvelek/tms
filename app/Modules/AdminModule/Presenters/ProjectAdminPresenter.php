<?php

namespace App\Modules\AdminModule;

use App\UI\LinkBuilder;

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
}

?>