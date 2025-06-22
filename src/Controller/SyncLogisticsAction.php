<?php

namespace Kuaidi100QueryBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Repository\LogisticsNumRepository;
use Kuaidi100QueryBundle\Service\LogisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SyncLogisticsAction extends AbstractController
{
    public function __construct(
        private readonly LogisticsNumRepository $logisticsNumRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly LogisticsService $service,
    ) {
    }

    #[Route(path: '/kuaidi100/sync-logistics', name: 'kuaidi100-sync-logistics')]
    public function __invoke(Request $request): Response
    {
        $params = $request->query->all();
        $param = $params['params'];
        $sign = $request->query->get('sign');

        $logisticsNum = $this->logisticsNumRepository->findOneBy([
            'number' => $param['nu'],
        ]);
        if (empty($logisticsNum)) {
            $logisticsNum = new LogisticsNum();
            $logisticsNum->setNumber($params['nu']);
            $logisticsNum->setCompany($params['com']);
            $this->entityManager->persist($logisticsNum);
        }
        $this->service->syncStatusToDb($logisticsNum, $param);
        $this->entityManager->flush();

        return $this->json($param);
    }
}