<?php

namespace Kuaidi100QueryBundle\Controller;

use Kuaidi100QueryBundle\Exception\AccountNotFoundException;
use Kuaidi100QueryBundle\Repository\AccountRepository;
use Kuaidi100QueryBundle\Request\Kuaidi100AutoNumber;
use Kuaidi100QueryBundle\Service\Kuaidi100Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AutoNumberController extends AbstractController
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly Kuaidi100Service $service,
    ) {
    }

    #[Route(path: '/kuaidi100/auto-number', name: 'kuaidi100_auto_number', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $sn = (string) $request->query->get('sn', '');

        $auto = new Kuaidi100AutoNumber();
        $auto->setNum($sn);
        $account = $this->accountRepository->findOneBy([
            'valid' => true,
        ]);
        if (null === $account) {
            throw new AccountNotFoundException();
        }
        $auto->setKey($account->getSignKey() ?? '');

        try {
            $res = $this->service->request($auto);

            return $this->json($res);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'code' => 'API_ERROR',
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
