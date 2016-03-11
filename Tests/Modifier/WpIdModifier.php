<?php

namespace Nz\MigrationBundle\Modifier;

use Doctrine\Common\Persistence\ManagerRegistry;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use AppBundle\Entity\Media\Media;
use Sonata\UserBundle\Model\UserManagerInterface;
use Sonata\UserBundle\Entity\UserManagerProxy;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class WpIdModifier implements ModifierInterface
{

    private $userManager;

    public function __construct(UserManagerProxy $userManager)
    {
        $this->userManager = $userManager;
    }

    public function getUserByWpId($wpid)
    {
        return $this->userManager->findOneBy(['wpId' => $wpid]);
    }

    public function modify($value, array $options = array())
    {

        $user = $this->getUserByWpId($value->getId());
        if ($user) {
            return $user;
        }
        return;
    }
}
