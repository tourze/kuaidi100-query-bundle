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

class PollAction extends AbstractController
{
    public function __construct(
        private readonly KuaidiCompanyRepository $companyRepository,
        private readonly Kuaidi100Service $service,
        private readonly AccountRepository $accountRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[Route(path: '/kuaidi100/poll', name: 'kuaidi100_poll')]
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
            throw new AccountNotFoundException();
        }
        $poll = new PollRequest();
        $poll->setAccount($account);
        $poll->setPhone($phone);
        $poll->setCom($com->getCode());
        $poll->setNum($deliverSn);
        $poll->setCallbackUrl($this->urlGenerator->generate('kuaidi100-sync-logistics', [], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->json($this->service->request($poll));
    }
}