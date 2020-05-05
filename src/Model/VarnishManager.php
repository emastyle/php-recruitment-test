<?php

namespace Snowdog\DevTest\Model;

use Snowdog\DevTest\Core\Database;

class VarnishManager
{

    /**
     * @var Database|\PDO
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getAllByUser(User $user)
    {
        $userId = $user->getUserId();
        $query = $this->database->prepare('SELECT * FROM varnishes WHERE user_id = :user order by varnish_id desc');
        $query->bindParam(':user', $userId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, Varnish::class);
    }

    public function getWebsites(Varnish $varnish)
    {
        $varnishId = $varnish->getVarnishId();
        $query = $this->database->prepare('
            SELECT * FROM websites
            INNER JOIN varnishes_websites ON (varnishes_websites.website_id = websites.website_id)
            WHERE varnishes_websites.varnish_id = :varnish'
        );
        $query->bindParam(':varnish', $varnishId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, Website::class);
    }

    public function getByWebsite(Website $website)
    {
        $websiteId = $website->getWebsiteId();
        $statement = $this->database->prepare('
            SELECT * FROM `varnishes`
            LEFT JOIN varnishes_websites ON (varnishes_websites.varnish_id = varnishes.varnish_id)
            WHERE varnishes_websites.website_id = :website'
        );
        $statement->bindParam(':website', $websiteId, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_CLASS, Varnish::class);
    }

    public function create(User $user, $ip)
    {
        $userId = $user->getUserId();
        $statement = $this->database->prepare('INSERT INTO varnishes (ip, user_id) VALUES (:ip, :user)');
        $statement->bindParam(':ip', $ip, \PDO::PARAM_STR);
        $statement->bindParam(':user', $userId, \PDO::PARAM_INT);
        $statement->execute();
        return $this->database->lastInsertId();
    }

    public function link($varnish, $website)
    {
        $statement = $this->database->prepare('REPLACE INTO `varnishes_websites` (varnish_id, website_id) VALUES (:varnish, :website)');
        $statement->bindParam(':varnish', $varnish, \PDO::PARAM_INT);
        $statement->bindParam(':website', $website, \PDO::PARAM_INT);
        return $statement->execute();
    }

    public function unlink($varnish, $website)
    {
        $statement = $this->database->prepare('DELETE FROM `varnishes_websites` WHERE varnish_id = :varnish AND website_id = :website');
        $statement->bindParam(':varnish', $varnish, \PDO::PARAM_INT);
        $statement->bindParam(':website', $website, \PDO::PARAM_INT);
        return $statement->execute();
    }

}
