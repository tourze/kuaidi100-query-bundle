<?php

namespace Kuaidi100QueryBundle\Controller;

use Kuaidi100QueryBundle\Exception\AccountNotFoundException;
use Kuaidi100QueryBundle\Repository\AccountRepository;
use Kuaidi100QueryBundle\Repository\KuaidiCompanyRepository;
use Kuaidi100QueryBundle\Request\PollRequest;
use Kuaidi100QueryBundle\Service\Kuaidi100Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PollController extends AbstractController
{
    public function __construct(
        private readonly KuaidiCompanyRepository $companyRepository,
        private readonly Kuaidi100Service $service,
        private readonly AccountRepository $accountRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[Route(path: '/kuaidi100/poll', name: 'kuaidi100_poll', methods: ['GET'])]
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
        $poll = new PollRequest();
        $poll->setAccount($account);
        $poll->setPhone($phone);
        $poll->setCom($com?->getCode() ?? '');
        $poll->setNum($deliverSn);
        $poll->setCallbackUrl($this->urlGenerator->generate('kuaidi100-sync-logistics', [], UrlGeneratorInterface::ABSOLUTE_URL));

        try {
            $res = $this->service->request($poll);

            return $this->json($res);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'code' => 'API_ERROR',
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
