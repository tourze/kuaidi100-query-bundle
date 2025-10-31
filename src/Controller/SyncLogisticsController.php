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

final class SyncLogisticsController extends AbstractController
{
    public function __construct(
        private readonly LogisticsNumRepository $logisticsNumRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly LogisticsService $service,
    ) {
    }

    #[Route(path: '/kuaidi100/sync-logistics', name: 'kuaidi100-sync-logistics', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $params = $request->query->all();
        $param = $this->normalizeParam($params['params'] ?? []);

        $trackingNumber = $param['nu'] ?? null;
        if (null === $trackingNumber || '' === $trackingNumber) {
            return $this->json(['error' => 'Missing tracking number']);
        }

        try {
            $logisticsNum = $this->findOrCreateLogisticsNum($trackingNumber, $param);
            $this->syncLogisticsData($logisticsNum, $param);
            $this->entityManager->flush();
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'code' => 'DATABASE_ERROR',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($param);
    }

    /**
     * @param mixed $param
     * @return array<string, mixed>
     */
    private function normalizeParam(mixed $param): array
    {
        if (!is_array($param)) {
            return [];
        }
        /** @var array<string, mixed> $param */
        return $param;
    }

    /**
     * @param array<string, mixed> $param
     */
    private function findOrCreateLogisticsNum(mixed $trackingNumber, array $param): LogisticsNum
    {
        $logisticsNum = $this->logisticsNumRepository->findOneBy([
            'number' => $trackingNumber,
        ]);

        if (null === $logisticsNum) {
            $logisticsNum = new LogisticsNum();
            $trackingNumberStr = is_string($trackingNumber) ? $trackingNumber : null;
            $companyCode = $param['com'] ?? 'unknown';
            $companyCodeStr = is_string($companyCode) ? $companyCode : null;

            $logisticsNum->setNumber($trackingNumberStr);
            $logisticsNum->setCompany($companyCodeStr);
            $this->entityManager->persist($logisticsNum);
        }

        return $logisticsNum;
    }

    /**
     * @param array<string, mixed> $param
     */
    private function syncLogisticsData(LogisticsNum $logisticsNum, array $param): void
    {
        if (isset($param['data']) && is_array($param['data']) && count($param['data']) > 0) {
            $this->service->syncStatusToDb($logisticsNum, $param);
        }
    }
}
