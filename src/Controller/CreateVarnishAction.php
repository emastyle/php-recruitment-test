<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\VarnishManager;

class CreateVarnishAction
{
    /**
     * @var VarnishManager
     */
    private $varnishManager;

    public function __construct(UserManager $userManager, VarnishManager $varnishManager)
    {
        $this->userManager = $userManager;
        $this->varnishManager = $varnishManager;
    }

    public function execute()
    {
        $ip = $_POST['ip'];
        if ( empty($ip)) {
            $_SESSION['flash'] = 'IP address cannot be empty!';
        } else {
            $user = $this->userManager->getByLogin($_SESSION['login']);
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                $_SESSION['flash'] = 'IP address not valid !';
            } else {
                if ($this->varnishManager->create($user, $ip)) {
                    $_SESSION['flash'] = 'Varnish ip ' . $ip . ' added !';
                } else {
                    $_SESSION['flash'] = 'Error adding varnish ip ' . $ip . ' !';
                }
            }
        }
        header('Location: /varnishes');
    }
}
