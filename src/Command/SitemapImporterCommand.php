<?php
/**
 * ImportSitemapCommand
 *
 * @copyright Copyright © 2020 Tech Rain S.p.a. All rights reserved.
 * @author
 */

namespace Snowdog\DevTest\Command;

use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Model\VarnishManager;
use Snowdog\DevTest\Model\WebsiteManager;
use Symfony\Component\Console\Output\OutputInterface;
use Snowdog\DevTest\Model\UserManager;
use SitemapImporter\SitemapImporter;

class SitemapImporterCommand
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
     * @var VarnishManager
     */
    private $varnishManager;
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var SitemapImporter
     */
    private $sitemapImporter;

    public function __construct(
        WebsiteManager $websiteManager,
        PageManager $pageManager,
        VarnishManager $varnishManager,
        UserManager $userManager,
        SitemapImporter $sitemapImporter
    )
    {
        $this->websiteManager = $websiteManager;
        $this->pageManager = $pageManager;
        $this->varnishManager = $varnishManager;
        $this->userManager = $userManager;
        $this->sitemapImporter = $sitemapImporter;
    }

    public function __invoke($userId, $sitemapUrl, OutputInterface $output)
    {
        try {
            $user = $this->userManager->getById($userId);
            if (empty($user)) {
                throw new \Exception('User ' . $userId . ' not exists!');
            }
            if (empty($sitemapUrl)) {
                throw new \Exception('Source empty!');
            }
            if (!$this->urlValidator($sitemapUrl)) {
                throw new \Exception('Sitemap URL not valid!');
            }

            $sitemapArray = $this->sitemapImporter->ImportSitemapByUrl($sitemapUrl);

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
            $output->writeln('Sitemap from <info>' . $sitemapUrl . '</info> imported!</comment>');
        } catch (\Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
        }
    }

    private function urlValidator($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return true;
        }
        return false;
    }
}
