<?php

namespace Kuaidi100QueryBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Repository\AccountRepository;
use Kuaidi100QueryBundle\Repository\KuaidiCompanyRepository;
use Kuaidi100QueryBundle\Repository\LogisticsNumRepository;
use Kuaidi100QueryBundle\Request\Kuaidi100AutoNumber;
use Kuaidi100QueryBundle\Request\Kuaidi100QueryRequest;
use Kuaidi100QueryBundle\Request\Kuaidi100Resolution;
use Kuaidi100QueryBundle\Request\PollRequest;
use Kuaidi100QueryBundle\Service\Kuaidi100Service;
use Kuaidi100QueryBundle\Service\LogisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route(path: '/kuaidi100')]
class QueryController extends AbstractController
{
    public function __construct(
        private readonly LogisticsNumRepository $logisticsNumRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(path: '/query')]
    public function query(
        Request $request,
        KuaidiCompanyRepository $companyRepository,
        Kuaidi100Service $service,
        LogisticsService $logisticsService,
        AccountRepository $accountRepository,
    ): Response {
        $company = $request->query->get('company');
        $deliverSn = $request->query->get('sn');
        $phone = $request->query->get('phone');

        $com = $companyRepository->findOneBy([
            'name' => $company,
        ]);

        $account = $accountRepository->findOneBy([
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
        $res = $service->request($apiRequest);

        $logisticsNum = $this->logisticsNumRepository->findOneBy([
            'number' => $deliverSn,
        ]);
        if (empty($logisticsNum)) {
            $logisticsNum = new LogisticsNum();
            $logisticsNum->setNumber($deliverSn);
            $logisticsNum->setCompany($company);
            $this->entityManager->persist($logisticsNum);
        }
        $logisticsService->syncStatusToDb($logisticsNum, $res);
        $this->entityManager->flush();

        return $this->json($res);
    }

    #[Route(path: '/sync-logistics', name: 'kuaidi100-sync-logistics')]
    public function syncLogistics(
        Request $request,
        LogisticsService $service,
    ): Response {
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
        $service->syncStatusToDb($logisticsNum, $param);
        $this->entityManager->flush();

        return $this->json($param);
    }

    #[Route(path: '/poll')]
    public function poll(
        Request $request,
        KuaidiCompanyRepository $companyRepository,
        Kuaidi100Service $service,
        AccountRepository $accountRepository,
        UrlGeneratorInterface $urlGenerator,
    ): Response {
        $company = $request->query->get('company');
        $deliverSn = $request->query->get('sn');
        $phone = $request->query->get('phone');

        $com = $companyRepository->findOneBy([
            'name' => $company,
        ]);

        $account = $accountRepository->findOneBy([
            'valid' => true,
        ]);
        if (empty($account)) {
            throw new \Exception('加密失败');
        }
        $poll = new PollRequest();
        $poll->setAccount($account);
        $poll->setPhone($phone);
        $poll->setCom($com->getCode());
        $poll->setNum($deliverSn);
        // $poll->setCallbackUrl($urlGenerator->generate('kuaidi100-sync-logistics',[], UrlGeneratorInterface::ABSOLUTE_URL));
        $poll->setCallbackUrl('https://ziwi-t.gzcrm.cn/kuaidi100/sync-logistics');

        return $this->json($service->request($poll));
    }

    #[Route(path: '/address-resolution')]
    public function addressResolution(
        Request $request,
        AccountRepository $accountRepository,
        Kuaidi100Service $service,
    ): Response {
        $content = $request->query->get('content', '');
        $imageUrl = $request->query->get('imageUrl', '');
        $pdfUrl = $request->query->get('pdfUrl', '');

        $resolution = new Kuaidi100Resolution();
        $resolution->setT(time());
        $resolution->setContent($content);
        $resolution->setImageUrl($imageUrl);
        $resolution->setPdfUrl($pdfUrl);
        $account = $accountRepository->findOneBy([
            'valid' => true,
        ]);
        if (empty($account)) {
            throw new \Exception('加密失败');
        }
        $resolution->setAccount($account);

        return $this->json($service->request($resolution));
    }

    #[Route(path: '/auto-number')]
    public function autoNumber(
        Request $request,
        AccountRepository $accountRepository,
        Kuaidi100Service $service,
    ): Response {
        $sn = $request->query->get('sn');

        $auto = new Kuaidi100AutoNumber();
        $auto->setNum($sn);
        $account = $accountRepository->findOneBy([
            'valid' => true,
        ]);
        if (empty($account)) {
            throw new \Exception('加密失败');
        }
        $auto->setKey($account->getSignKey());

        return $this->json($service->request($auto));
    }
}
