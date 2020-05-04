<?php

namespace Snowdog\DevTest\Model;

use Snowdog\DevTest\Core\Database;

class PageManager
{

    /**
     * @var Database|\PDO
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getAllByWebsite(Website $website)
    {
        $websiteId = $website->getWebsiteId();
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT * FROM pages WHERE website_id = :website');
        $query->bindParam(':website', $websiteId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, Page::class);
    }

    public function create(Website $website, $url)
    {
        $websiteId = $website->getWebsiteId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('INSERT INTO pages (url, website_id) VALUES (:url, :website)');
        $statement->bindParam(':url', $url, \PDO::PARAM_STR);
        $statement->bindParam(':website', $websiteId, \PDO::PARAM_INT);
        $statement->execute();
        return $this->database->lastInsertId();
    }

    public function updatePageLastVisit(Page $page)
    {
        $lastVisitDate = new \DateTime();
        $lastVisit = $lastVisitDate->format('Y-m-d H:i:s');
        $pageId = $page->getPageId();
        $query = $this->database->prepare('UPDATE pages SET last_visit = :last_visit WHERE page_id = :page');
        $query->bindParam(':last_visit', $lastVisit, \PDO::PARAM_STR);
        $query->bindParam(':page', $pageId, \PDO::PARAM_INT);
        return $query->execute();
    }
}

