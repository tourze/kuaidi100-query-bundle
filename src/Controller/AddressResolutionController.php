<?php

namespace Kuaidi100QueryBundle\Controller;

use Kuaidi100QueryBundle\Exception\AccountNotFoundException;
use Kuaidi100QueryBundle\Repository\AccountRepository;
use Kuaidi100QueryBundle\Request\Kuaidi100Resolution;
use Kuaidi100QueryBundle\Service\Kuaidi100Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AddressResolutionController extends AbstractController
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly Kuaidi100Service $service,
    ) {
    }

    #[Route(path: '/kuaidi100/address-resolution', name: 'kuaidi100_address_resolution', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $content = (string) $request->query->get('content', '');
        $imageUrl = (string) $request->query->get('imageUrl', '');
        $pdfUrl = (string) $request->query->get('pdfUrl', '');

        $resolution = new Kuaidi100Resolution();
        $resolution->setT((string) time());
        $resolution->setContent($content);
        $resolution->setImageUrl($imageUrl);
        $resolution->setPdfUrl($pdfUrl);
        $account = $this->accountRepository->findOneBy([
            'valid' => true,
        ]);
        if (null === $account) {
            throw new AccountNotFoundException();
        }
        $resolution->setAccount($account);

        try {
            $res = $this->service->request($resolution);

            return $this->json($res);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'code' => 'API_ERROR',
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
