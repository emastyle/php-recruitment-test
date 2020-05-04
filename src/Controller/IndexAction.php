<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\User;
use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\WebsiteManager;
use Snowdog\DevTest\Model\PageManager;

class IndexAction
{

    /**
     * @var WebsiteManager
     */
    private $websiteManager;

    /**
     * @var
     */
    private $pageManager;

    /**
     * @var User
     */
    private $user;

    public function __construct(UserManager $userManager, WebsiteManager $websiteManager, PageManager $pageManager)
    {
        $this->websiteManager = $websiteManager;
        $this->pageManager = $pageManager;
        if (isset($_SESSION['login'])) {
            $this->user = $userManager->getByLogin($_SESSION['login']);
        }
    }

    protected function getWebsites()
    {
        if($this->user) {
            return $this->websiteManager->getAllByUser($this->user);
        }
        return [];
    }

    public function execute()
    {
        require __DIR__ . '/../view/index.phtml';
    }

    protected function getUserTotalPages()
    {
        if ($user = $this->user) {
            return $this->pageManager->getUserTotalPages($user);
        }
        return 0;
    }

    protected function getLeastVisitedPage() {
        return $this->getUserPagesStats('DESC');
    }

    protected function getMostVisitedPage() {
        return $this->getUserPagesStats();
    }

    private function getUserPagesStats($sort  = 'ASC') {
        if ($user = $this->user) {
            $page = $this->pageManager->getVisitedPagesByUser($user, $sort);
            if ($page && $websiteId = $page->getWebsiteId()) {
                if ($website = $this->websiteManager->getById($websiteId)) {
                    return $website->getHostname() . '/' . $page->getUrl();
                }
            }
        }
        return null;
    }
}
