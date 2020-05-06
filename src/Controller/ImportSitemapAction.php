<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\WebsiteManager;
use Snowdog\DevTest\Model\PageManagerManager;
use SitemapImporter\SitemapImporter;

class ImportSitemapAction
{
    /**
     * @var WebsiteManager
     */
    private $websiteManager;

    /**
     * @var PageManager
     */
    private $pageManager;

    /**
     * @var SitemapImporter
     */
    private $sitemapImporter;

    public function __construct(UserManager $userManager, WebsiteManager $websiteManager, PageManager $pageManager, SitemapImporter $sitemapImporter)
    {
        $this->userManager = $userManager;
        $this->websiteManager = $websiteManager;
        $this->pageManager = $pageManager;
        $this->sitemapImporter = $sitemapImporter;
    }

    public function execute()
    {
        $sitemapUrl = isset($_POST['sitemapUrl']) ? $_POST['sitemapUrl'] : null;
        $sitemapXML = isset($_POST['sitemapXML']) ? $_POST['sitemapXML'] : null;
        $sitemapArray = [];

        if ( empty($sitemapUrl) && empty($sitemapXML)) {
            $_SESSION['flash'] = 'No sitemap data provided!';
        } else {
            try {
                $user = $this->userManager->getByLogin($_SESSION['login']);
                if (!empty($sitemapUrl)) {
                    $sitemapArray = $this->sitemapImporter->ImportSitemapByUrl($sitemapUrl);
                } else if (!empty($sitemapXML)) {
                    $sitemapArray = $this->sitemapImporter->ImportSitemapByXML($sitemapXML);
                }

                foreach ($sitemapArray as $host => $pages) {
                    foreach ($pages as $url) {
                        $website = $this->websiteManager->getByHostname($host);
                        if (empty($website)) {
                            $createdWebsiteId = $this->websiteManager->create($user, $host, $host);
                            $website = $this->websiteManager->getById($createdWebsiteId);
                        }
                        $this->pageManager->create($website, $url);
                    }
                }
                $_SESSION['flash'] = 'Sitemap imported!';
            } catch (\Exception $exception) {
                $_SESSION['flash'] = $exception->getMessage();
            }
        }

        header('Location: /');
    }
}
