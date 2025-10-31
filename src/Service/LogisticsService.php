<?php

namespace Kuaidi100QueryBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Entity\LogisticsStatus;
use Kuaidi100QueryBundle\Enum\LogisticsStateEnum;
use Kuaidi100QueryBundle\Exception\AccountNotFoundException;
use Kuaidi100QueryBundle\Exception\LogisticsCompanyNotFoundException;
use Kuaidi100QueryBundle\Repository\KuaidiCompanyRepository;
use Kuaidi100QueryBundle\Repository\LogisticsStatusRepository;
use Kuaidi100QueryBundle\Request\Kuaidi100QueryRequest;
use Kuaidi100QueryBundle\Request\PollRequest;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Autoconfigure(public: true)]
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
        if (null === $com) {
            throw new LogisticsCompanyNotFoundException();
        }

        $account = $number->getAccount();
        if (null === $account) {
            throw new AccountNotFoundException();
        }

        $apiRequest = new Kuaidi100QueryRequest();
        $apiRequest->setAccount($account);
        $comCode = $com->getCode();
        if (null === $comCode) {
            throw new \InvalidArgumentException('快递公司编码不能为空');
        }
        $apiRequest->setCom($comCode);

        $numberValue = $number->getNumber();
        if (null === $numberValue) {
            throw new \InvalidArgumentException('物流单号不能为空');
        }
        $apiRequest->setNum($numberValue);

        $apiRequest->setPhone($number->getPhoneNumber() ?? '');
        $res = $this->apiService->request($apiRequest);
        // 确保响应是数组类型
        if (!is_array($res)) {
            throw new \InvalidArgumentException('API响应格式错误：期望数组类型');
        }
        /** @var array<string, mixed> $res */
        $this->syncStatusToDb($number, $res);

        // 保存同步时间
        $number->setSyncTime(new \DateTimeImmutable());
        $this->entityManager->persist($number);
        $this->entityManager->flush();
    }

    /**
     * @param LogisticsNum $number
     * @param array<string, mixed> $param
     */
    public function syncStatusToDb(LogisticsNum $number, array $param): void
    {
        // 确保有数据项需要同步
        if (!isset($param['data']) || !is_array($param['data']) || 0 === count($param['data'])) {
            return;
        }

        $sn = $this->ensureStringValue($param['nu'] ?? '');
        $companyCode = $this->ensureStringValue($param['com'] ?? 'unknown');
        $state = $param['state'] ?? null;
        $areaCenter = $param['areaCenter'] ?? null;

        foreach ($param['data'] as $item) {
            $this->processSingleLogisticsItem($number, $item, $sn, $companyCode, $state, $areaCenter);
        }
        $this->entityManager->flush();
    }

    /**
     * 处理单个物流数据项
     *
     * @param LogisticsNum $number
     * @param mixed $item
     * @param string $sn
     * @param string $companyCode
     * @param mixed $state
     * @param mixed $areaCenter
     */
    private function processSingleLogisticsItem(
        LogisticsNum $number,
        mixed $item,
        string $sn,
        string $companyCode,
        mixed $state,
        mixed $areaCenter
    ): void {
        // 跳过无效的数据项
        if (!is_array($item) || !isset($item['context']) || !isset($item['ftime'])) {
            return;
        }

        $context = $this->ensureStringValue($item['context']);
        $ftime = $this->ensureStringValue($item['ftime']);
        $flag = md5($context);

        $logistics = $this->logisticsStatusRepository->findOneBy([
            'number' => $number,
            'sn' => $sn,
            'flag' => $flag,
        ]);

        if (null !== $logistics) {
            return;
        }

        $logistics = new LogisticsStatus();
        $logistics->setNumber($number);
        $logistics->setSn($sn);
        $logistics->setContext($context);
        $logistics->setFtime($ftime);

        // 安全处理可能不存在的字段
        if (null !== $state) {
            $stateValue = is_string($state) || is_int($state) ? $state : null;
            if (null !== $stateValue) {
                $logistics->setState(LogisticsStateEnum::tryFrom($stateValue));
            }
        }

        $logistics->setCompanyCode($companyCode);

        if (null !== $areaCenter) {
            $logistics->setAreaCenter($this->ensureStringValue($areaCenter));
        }

        $logistics->setFlag($flag);
        $this->entityManager->persist($logistics);
    }

    /**
     * 确保值是字符串类型
     */
    private function ensureStringValue(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_scalar($value)) {
            return (string)$value;
        }

        return '';
    }

    public function subscribe(LogisticsNum $number): void
    {
        if (true === $number->isSubscribed()) {
            return;
        }

        $account = $number->getAccount();
        if (null === $account) {
            throw new AccountNotFoundException();
        }

        $company = $number->getCompany();
        if (null === $company) {
            throw new \InvalidArgumentException('快递公司不能为空');
        }

        $numberValue = $number->getNumber();
        if (null === $numberValue) {
            throw new \InvalidArgumentException('物流单号不能为空');
        }

        $pollRequest = new PollRequest();
        $pollRequest->setAccount($account);
        $pollRequest->setPhone($number->getPhoneNumber() ?? '');
        $pollRequest->setCom($company);
        $pollRequest->setNum($numberValue);
        $pollRequest->setCallbackUrl($this->urlGenerator->generate('kuaidi100-sync-logistics', [], UrlGeneratorInterface::ABSOLUTE_URL));
        $this->apiService->request($pollRequest);

        $number->setSubscribed(true);
        $this->entityManager->persist($number);
        $this->entityManager->flush();
    }
}
