<?php

namespace Nz\MigrationBundle\Controller\Crud;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Media\Media;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Nz\MigrationBundle\Admin\Traits\MigrationTrait;
use Nz\MigrationBundle\Migrator\MigratorHandler;
use Nz\MigrationBundle\Entity\Log as MigrationLog;
use Nz\MigrationBundle\Entity\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nz\MigrationBundle\Diff\Diff;
use Nz\MigrationBundle\Diff\Renderer\Html\SideBySide;

class LogAdminController extends Controller
{

    public function diffAction($id, Request $request = null)
    {
        $log = $this->getDoctrine()->getManagerForClass(Log::class)->find(Log::class, $id);

        if (!$log || !$log->getTargetId() || $log->getError()) {
            throw new NotFoundHttpException('Log not found or invalid');
        }

        $source = $this->getDoctrine()->getManagerForClass($log->getSource())->find($log->getSource(), $log->getSourceId());
        $target = $this->getDoctrine()->getManagerForClass($log->getTarget())->find($log->getTarget(), $log->getTargetId());

        /*
          $a = explode("\n", $source->getContent());
          $b = explode("\n", $target->getContent());
          $options = array(
          );

          $a = array_values(array_filter($a));
          $b = array_values(array_filter($b));

          $diff = new Diff($a, $b, $options);
          $html = $diff->Render(new SideBySide);
         */

        return $this->render($this->admin->getTemplate('diff'), array(
                'action' => 'diff',
                'log' => $log,
                /* 'diff' => $html, */
                'source' => $source,
                'target' => $target,
        ));
    }
}
