<?php

namespace Kuaidi100QueryBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Exception\AccountNotFoundException;
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

final class QueryController extends AbstractController
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

    #[Route(path: '/kuaidi100/query', name: 'kuaidi100_query', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $company = $request->query->get('company');
        $deliverSn = (string) $request->query->get('sn', '');
        $phone = (string) $request->query->get('phone', '');

        try {
            $com = $this->companyRepository->findOneBy([
                'name' => $company,
            ]);
        } catch (\Throwable) {
            // 如果公司表不存在或查询失败，使用 null
            $com = null;
        }

        try {
            $account = $this->accountRepository->findOneBy([
                'valid' => true,
            ]);
        } catch (\Throwable) {
            // 如果账户表不存在或查询失败
            $account = null;
        }
        if (null === $account) {
            throw new AccountNotFoundException();
        }
        $apiRequest = new Kuaidi100QueryRequest();
        $apiRequest->setAccount($account);
        $apiRequest->setCom($com?->getCode() ?? '');
        $apiRequest->setNum($deliverSn);
        $apiRequest->setPhone($phone);

        try {
            $res = $this->service->request($apiRequest);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'code' => 'API_ERROR',
            ], Response::HTTP_BAD_REQUEST);
        }

        $logisticsNum = $this->logisticsNumRepository->findOneBy([
            'number' => $deliverSn,
        ]);
        if (null === $logisticsNum) {
            $logisticsNum = new LogisticsNum();
            $logisticsNum->setNumber($deliverSn);
            $logisticsNum->setCompany((string) $company);
            $this->entityManager->persist($logisticsNum);
        }
        // 确保 $res 是正确的数组类型
        if (!is_array($res)) {
            $apiResponse = [];
        } else {
            /** @var array<string, mixed> $apiResponse */
            $apiResponse = $res;
        }
        $this->logisticsService->syncStatusToDb($logisticsNum, $apiResponse);
        $this->entityManager->flush();

        return $this->json($res);
    }
}
