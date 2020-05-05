<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\Varnish;
use Snowdog\DevTest\Model\VarnishManager;
use Snowdog\DevTest\Model\Website;

class CreateVarnishLinkAction
{
    /**
     * @var UserManager
     */
    private $userManager;
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
        $varnishId = $_POST['varnishId'];
        $websiteId = $_POST['websiteId'];
        $isChecked = ($_POST['checked'] == 'true') ? TRUE : FALSE;
        try {
            if ($isChecked) {
                $this->varnishManager->link($varnishId, $websiteId);
                $msg = 'Varnish server linked successfully!';
            } else {
                $this->varnishManager->unlink($varnishId, $websiteId);
                $msg = 'Varnish server unlinked.';
            }
            echo json_encode([
                'status' => 'success',
                'message' => $msg
            ]);
        } catch (\Exception $ex) {
            echo json_encode([
                'status' => 'error',
                'message' => $ex->getMessage()
            ]);
        }
    }
}
