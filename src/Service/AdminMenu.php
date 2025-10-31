<?php

declare(strict_types=1);

namespace Kuaidi100QueryBundle\Service;

use Knp\Menu\ItemInterface;
use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Entity\KuaidiCompany;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Entity\LogisticsStatus;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        // 创建或获取快递100查询管理菜单组
        if (null === $item->getChild('快递100查询管理')) {
            $item->addChild('快递100查询管理');
        }

        $kuaidi100Menu = $item->getChild('快递100查询管理');
        if (null === $kuaidi100Menu) {
            throw new \RuntimeException('快递100查询管理菜单项创建失败');
        }

        // 添加快递账号管理
        $kuaidi100Menu
            ->addChild('快递账号管理')
            ->setUri($this->linkGenerator->getCurdListPage(Account::class))
            ->setAttribute('icon', 'fas fa-user-cog')
            ->setExtra('description', '管理快递100接口账号')
        ;

        // 添加快递公司管理
        $kuaidi100Menu
            ->addChild('快递公司管理')
            ->setUri($this->linkGenerator->getCurdListPage(KuaidiCompany::class))
            ->setAttribute('icon', 'fas fa-building')
            ->setExtra('description', '管理快递公司信息')
        ;

        // 添加物流单号管理
        $kuaidi100Menu
            ->addChild('物流单号管理')
            ->setUri($this->linkGenerator->getCurdListPage(LogisticsNum::class))
            ->setAttribute('icon', 'fas fa-barcode')
            ->setExtra('description', '管理物流单号信息')
        ;

        // 添加物流状态管理
        $kuaidi100Menu
            ->addChild('物流状态管理')
            ->setUri($this->linkGenerator->getCurdListPage(LogisticsStatus::class))
            ->setAttribute('icon', 'fas fa-shipping-fast')
            ->setExtra('description', '查看物流状态详情')
        ;
    }
}
