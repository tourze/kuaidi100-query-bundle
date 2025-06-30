<?php

namespace Kuaidi100QueryBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Entity\LogisticsStatus;
use Kuaidi100QueryBundle\Enum\LogisticsStateEnum;
use Kuaidi100QueryBundle\Exception\LogisticsCompanyNotFoundException;
use Kuaidi100QueryBundle\Repository\KuaidiCompanyRepository;
use Kuaidi100QueryBundle\Repository\LogisticsStatusRepository;
use Kuaidi100QueryBundle\Request\Kuaidi100QueryRequest;
use Kuaidi100QueryBundle\Request\PollRequest;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LogisticsService
{
    public function __construct(
        private readonly LogisticsStatusRepository $logisticsStatusRepository,
        private readonly KuaidiCompanyRepository $companyRepository,
        private readonly Kuaidi100Service $apiService,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function queryAndSync(LogisticsNum $number): void
    {
        $com = $this->companyRepository->findOneBy([
            'name' => $number->getCompany(),
        ]);
        if ($com === null) {
            throw new LogisticsCompanyNotFoundException();
        }

        $apiRequest = new Kuaidi100QueryRequest();
        $apiRequest->setAccount($number->getAccount());
        $apiRequest->setCom($com->getCode());
        $apiRequest->setNum($number->getNumber());
        $apiRequest->setPhone($number->getPhone());
        $res = $this->apiService->request($apiRequest);
        $this->syncStatusToDb($number, $res);

        // 保存同步时间
        $number->setSyncTime(new \DateTimeImmutable());
        $this->entityManager->persist($number);
        $this->entityManager->flush();
    }

    public function syncStatusToDb(LogisticsNum $number, array $param): void
    {
        foreach ($param['data'] as $item) {
            $logistics = $this->logisticsStatusRepository->findOneBy([
                'number' => $number,
                'sn' => $param['nu'],
                'flag' => md5($item['context']),
            ]);
            if ($logistics !== null) {
                continue;
            }

            $logistics = new LogisticsStatus();
            $logistics->setNumber($number);
            $logistics->setSn($param['nu']);
            $logistics->setContext($item['context']);
            $logistics->setFtime($item['ftime']);
            $logistics->setState(LogisticsStateEnum::tryFrom($param['state']));
            $logistics->setCompanyCode($param['com']);
            if (isset($param['areaCenter'])) {
                $logistics->setAreaCenter($param['areaCenter']);
            }
            $logistics->setFlag(md5($item['context']));
            $this->entityManager->persist($logistics);
        }
        $this->entityManager->flush();
    }

    public function subscribe(LogisticsNum $number): void
    {
        if ($number->isSubscribed()) {
            return;
        }

        $pollRequest = new PollRequest();
        $pollRequest->setAccount($number->getAccount());
        $pollRequest->setPhone($number->getPhone());
        $pollRequest->setCom($number->getCompany());
        $pollRequest->setNum($number->getNumber());
        $pollRequest->setCallbackUrl($this->urlGenerator->generate('kuaidi100-sync-logistics', [], UrlGeneratorInterface::ABSOLUTE_URL));
        $this->apiService->request($pollRequest);

        $number->setSubscribed(true);
        $this->entityManager->persist($number);
        $this->entityManager->flush();
    }
}
