<?php
namespace Snowdog\DevTest\Migration;

use Snowdog\DevTest\Core\Database;

class Version4
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
        $this->createVarnishTables();
    }

    private function createVarnishTables()
    {
        $query = <<<SQL
        CREATE TABLE `varnishes` 
        (
          `varnish_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `ip` varchar(15) NOT NULL,
          `user_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`varnish_id`),
          UNIQUE KEY `ip_UNIQUE` (`ip`),
          KEY `VARNISH_USER_ID_FK` (`user_id`),
          CONSTRAINT `VARNISH_USER_ID_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
        );

        CREATE TABLE `varnishes_websites` 
        (
            `varnish_id` int(10) unsigned NOT NULL,
            `website_id` int(10) unsigned NOT NULL,
            PRIMARY KEY (`varnish_id`,`website_id`),
            CONSTRAINT `VARNISH_WEBSITE_VARNISH_ID_FK` FOREIGN KEY (`varnish_id`) REFERENCES `varnishes` (`varnish_id`),
            CONSTRAINT `VARNISH_WEBSITE_WEBSITE_ID_FK` FOREIGN KEY (`website_id`) REFERENCES `websites` (`website_id`)
        );
SQL;
        $this->database->exec($query);
    }
}
