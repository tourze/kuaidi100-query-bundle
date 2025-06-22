<?php

namespace Kuaidi100QueryBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Repository\AccountRepository;
use Kuaidi100QueryBundle\Repository\KuaidiCompanyRepository;
use Kuaidi100QueryBundle\Repository\LogisticsNumRepository;
use Kuaidi100QueryBundle\Request\Kuaidi100QueryRequest;
use Kuaidi100QueryBundle\Service\Kuaidi100Service;
use Kuaidi100QueryBundle\Service\LogisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class QueryAction extends AbstractController
{
    public function __construct(
        private readonly LogisticsNumRepository $logisticsNumRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly KuaidiCompanyRepository $companyRepository,
        private readonly Kuaidi100Service $service,
        private readonly LogisticsService $logisticsService,
        private readonly AccountRepository $accountRepository,
    ) {
    }

    #[Route(path: '/kuaidi100/query', name: 'kuaidi100_query')]
    public function __invoke(Request $request): Response
    {
        $company = $request->query->get('company');
        $deliverSn = $request->query->get('sn');
        $phone = $request->query->get('phone');

        $com = $this->companyRepository->findOneBy([
            'name' => $company,
        ]);

        $account = $this->accountRepository->findOneBy([
            'valid' => true,
        ]);
        if (empty($account)) {
            throw new \Exception('加密失败');
        }
        $apiRequest = new Kuaidi100QueryRequest();
        $apiRequest->setAccount($account);
        $apiRequest->setCom($com->getCode());
        $apiRequest->setNum($deliverSn);
        $apiRequest->setPhone($phone);
        $res = $this->service->request($apiRequest);

        $logisticsNum = $this->logisticsNumRepository->findOneBy([
            'number' => $deliverSn,
        ]);
        if (empty($logisticsNum)) {
            $logisticsNum = new LogisticsNum();
            $logisticsNum->setNumber($deliverSn);
            $logisticsNum->setCompany($company);
            $this->entityManager->persist($logisticsNum);
        }
        $this->logisticsService->syncStatusToDb($logisticsNum, $res);
        $this->entityManager->flush();

        return $this->json($res);
    }
}