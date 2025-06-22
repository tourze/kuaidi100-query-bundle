<?php

namespace Kuaidi100QueryBundle\Controller;

use Kuaidi100QueryBundle\Repository\AccountRepository;
use Kuaidi100QueryBundle\Request\Kuaidi100Resolution;
use Kuaidi100QueryBundle\Service\Kuaidi100Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddressResolutionAction extends AbstractController
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly Kuaidi100Service $service,
    ) {
    }

    #[Route(path: '/kuaidi100/address-resolution', name: 'kuaidi100_address_resolution')]
    public function __invoke(Request $request): Response
    {
        $content = $request->query->get('content', '');
        $imageUrl = $request->query->get('imageUrl', '');
        $pdfUrl = $request->query->get('pdfUrl', '');

        $resolution = new Kuaidi100Resolution();
        $resolution->setT((string) time());
        $resolution->setContent($content);
        $resolution->setImageUrl($imageUrl);
        $resolution->setPdfUrl($pdfUrl);
        $account = $this->accountRepository->findOneBy([
            'valid' => true,
        ]);
        if (empty($account)) {
            throw new \Exception('加密失败');
        }
        $resolution->setAccount($account);

        return $this->json($this->service->request($resolution));
    }
}