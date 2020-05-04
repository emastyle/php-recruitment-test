<?php

namespace Snowdog\DevTest\Migration;

use Snowdog\DevTest\Core\Database;

class Version3
{
    /**
     * @var Database
     */
    private $database;

    public function __construct(
        Database $database
    ) {
        $this->database = $database;
    }

    public function __invoke()
    {
        $this->addPagesLastVisitDatetime();
    }

    private function addPagesLastVisitDatetime()
    {
        $createQuery = <<<SQL
    ALTER TABLE `pages` ADD `last_visit` DATETIME NULL AFTER `website_id`;
SQL;
        $this->database->exec($createQuery);
    }
}
