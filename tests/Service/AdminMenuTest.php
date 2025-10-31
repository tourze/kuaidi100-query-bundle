<?php

declare(strict_types=1);

namespace Kuaidi100QueryBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use Kuaidi100QueryBundle\Service\AdminMenu;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * AdminMenu 单元测试
 *
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private ItemInterface $item;

    public function testInvokeMethod(): void
    {
        // 测试 AdminMenu 的 __invoke 方法正常工作
        $this->expectNotToPerformAssertions();

        try {
            $adminMenu = self::getService(AdminMenu::class);
            ($adminMenu)($this->item);
        } catch (\Throwable $e) {
            self::fail('AdminMenu __invoke method should not throw exception: ' . $e->getMessage());
        }
    }

    public function testMenuStructure(): void
    {
        // 测试菜单结构是否正确创建
        $adminMenu = self::getService(AdminMenu::class);

        // 创建一个真实的菜单项来测试
        $rootItem = $this->createMock(ItemInterface::class);
        $kuaidi100MenuItem = $this->createMock(ItemInterface::class);

        // 创建4个独立的子菜单项Mock，每个对应一个菜单项的链式调用
        $accountMenuItem = $this->createMock(ItemInterface::class);
        $companyMenuItem = $this->createMock(ItemInterface::class);
        $numMenuItem = $this->createMock(ItemInterface::class);
        $statusMenuItem = $this->createMock(ItemInterface::class);

        // 设置期望的菜单结构调用 - getChild会被调用两次
        $rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('快递100查询管理')
            ->willReturnOnConsecutiveCalls(null, $kuaidi100MenuItem)
        ;

        $rootItem->expects($this->once())
            ->method('addChild')
            ->with('快递100查询管理')
            ->willReturn($kuaidi100MenuItem)
        ;

        // 设置子菜单期望 - addChild 返回对应的子菜单项并支持链式调用
        $kuaidi100MenuItem->expects($this->exactly(4))
            ->method('addChild')
            ->willReturnCallback(function (string $name) use ($accountMenuItem, $companyMenuItem, $numMenuItem, $statusMenuItem) {
                return match ($name) {
                    '快递账号管理' => $accountMenuItem,
                    '快递公司管理' => $companyMenuItem,
                    '物流单号管理' => $numMenuItem,
                    '物流状态管理' => $statusMenuItem,
                    default => throw new \InvalidArgumentException("Unexpected menu name: {$name}"),
                };
            })
        ;

        // 设置每个子菜单项的链式调用方法期望
        // 快递账号管理
        $accountMenuItem->expects($this->once())
            ->method('setUri')
            ->willReturn($accountMenuItem)
        ;
        $accountMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-user-cog')
            ->willReturn($accountMenuItem)
        ;
        $accountMenuItem->expects($this->once())
            ->method('setExtra')
            ->with('description', '管理快递100接口账号')
            ->willReturn($accountMenuItem)
        ;

        // 快递公司管理
        $companyMenuItem->expects($this->once())
            ->method('setUri')
            ->willReturn($companyMenuItem)
        ;
        $companyMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-building')
            ->willReturn($companyMenuItem)
        ;
        $companyMenuItem->expects($this->once())
            ->method('setExtra')
            ->with('description', '管理快递公司信息')
            ->willReturn($companyMenuItem)
        ;

        // 物流单号管理
        $numMenuItem->expects($this->once())
            ->method('setUri')
            ->willReturn($numMenuItem)
        ;
        $numMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-barcode')
            ->willReturn($numMenuItem)
        ;
        $numMenuItem->expects($this->once())
            ->method('setExtra')
            ->with('description', '管理物流单号信息')
            ->willReturn($numMenuItem)
        ;

        // 物流状态管理
        $statusMenuItem->expects($this->once())
            ->method('setUri')
            ->willReturn($statusMenuItem)
        ;
        $statusMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-shipping-fast')
            ->willReturn($statusMenuItem)
        ;
        $statusMenuItem->expects($this->once())
            ->method('setExtra')
            ->with('description', '查看物流状态详情')
            ->willReturn($statusMenuItem)
        ;

        // 执行测试
        ($adminMenu)($rootItem);
    }

    public function testExistingMenuGroup(): void
    {
        // 测试当菜单组已存在时的行为
        $adminMenu = self::getService(AdminMenu::class);

        $rootItem = $this->createMock(ItemInterface::class);
        $existingKuaidi100MenuItem = $this->createMock(ItemInterface::class);

        // 创建4个独立的子菜单项Mock，每个对应一个菜单项的链式调用
        $accountMenuItem = $this->createMock(ItemInterface::class);
        $companyMenuItem = $this->createMock(ItemInterface::class);
        $numMenuItem = $this->createMock(ItemInterface::class);
        $statusMenuItem = $this->createMock(ItemInterface::class);

        // 模拟菜单组已存在的情况 - 会被调用两次
        $rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('快递100查询管理')
            ->willReturn($existingKuaidi100MenuItem)
        ;

        // 不应该再次添加菜单组
        $rootItem->expects($this->never())
            ->method('addChild')
        ;

        // 应该在已存在的菜单组中添加子菜单
        $existingKuaidi100MenuItem->expects($this->exactly(4))
            ->method('addChild')
            ->willReturnCallback(function (string $name) use ($accountMenuItem, $companyMenuItem, $numMenuItem, $statusMenuItem) {
                return match ($name) {
                    '快递账号管理' => $accountMenuItem,
                    '快递公司管理' => $companyMenuItem,
                    '物流单号管理' => $numMenuItem,
                    '物流状态管理' => $statusMenuItem,
                    default => throw new \InvalidArgumentException("Unexpected menu name: {$name}"),
                };
            })
        ;

        // 设置每个子菜单项的链式调用方法期望
        // 快递账号管理
        $accountMenuItem->expects($this->once())
            ->method('setUri')
            ->willReturn($accountMenuItem)
        ;
        $accountMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-user-cog')
            ->willReturn($accountMenuItem)
        ;
        $accountMenuItem->expects($this->once())
            ->method('setExtra')
            ->with('description', '管理快递100接口账号')
            ->willReturn($accountMenuItem)
        ;

        // 快递公司管理
        $companyMenuItem->expects($this->once())
            ->method('setUri')
            ->willReturn($companyMenuItem)
        ;
        $companyMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-building')
            ->willReturn($companyMenuItem)
        ;
        $companyMenuItem->expects($this->once())
            ->method('setExtra')
            ->with('description', '管理快递公司信息')
            ->willReturn($companyMenuItem)
        ;

        // 物流单号管理
        $numMenuItem->expects($this->once())
            ->method('setUri')
            ->willReturn($numMenuItem)
        ;
        $numMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-barcode')
            ->willReturn($numMenuItem)
        ;
        $numMenuItem->expects($this->once())
            ->method('setExtra')
            ->with('description', '管理物流单号信息')
            ->willReturn($numMenuItem)
        ;

        // 物流状态管理
        $statusMenuItem->expects($this->once())
            ->method('setUri')
            ->willReturn($statusMenuItem)
        ;
        $statusMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-shipping-fast')
            ->willReturn($statusMenuItem)
        ;
        $statusMenuItem->expects($this->once())
            ->method('setExtra')
            ->with('description', '查看物流状态详情')
            ->willReturn($statusMenuItem)
        ;

        // 执行测试
        ($adminMenu)($rootItem);
    }

    public function testMenuItemsConfiguration(): void
    {
        // 测试菜单项的具体配置
        $adminMenu = self::getService(AdminMenu::class);

        $rootItem = $this->createMock(ItemInterface::class);
        $kuaidi100MenuItem = $this->createMock(ItemInterface::class);

        // 创建4个独立的子菜单项Mock，每个对应一个菜单项的链式调用
        $accountMenuItem = $this->createMock(ItemInterface::class);
        $companyMenuItem = $this->createMock(ItemInterface::class);
        $numMenuItem = $this->createMock(ItemInterface::class);
        $statusMenuItem = $this->createMock(ItemInterface::class);

        $rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('快递100查询管理')
            ->willReturnOnConsecutiveCalls(null, $kuaidi100MenuItem)
        ;
        $rootItem->method('addChild')->willReturn($kuaidi100MenuItem);

        // 验证特定的菜单项配置
        $kuaidi100MenuItem->expects($this->exactly(4))
            ->method('addChild')
            ->willReturnCallback(function (string $name) use ($accountMenuItem, $companyMenuItem, $numMenuItem, $statusMenuItem) {
                return match ($name) {
                    '快递账号管理' => $accountMenuItem,
                    '快递公司管理' => $companyMenuItem,
                    '物流单号管理' => $numMenuItem,
                    '物流状态管理' => $statusMenuItem,
                    default => throw new \InvalidArgumentException("Unexpected menu name: {$name}"),
                };
            })
        ;

        // 验证图标设置 - 每个菜单项都有特定的图标
        $accountMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-user-cog')
            ->willReturn($accountMenuItem)
        ;
        $companyMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-building')
            ->willReturn($companyMenuItem)
        ;
        $numMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-barcode')
            ->willReturn($numMenuItem)
        ;
        $statusMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-shipping-fast')
            ->willReturn($statusMenuItem)
        ;

        // 验证描述设置 - 每个菜单项都有特定的描述
        $accountMenuItem->expects($this->once())
            ->method('setExtra')
            ->with('description', '管理快递100接口账号')
            ->willReturn($accountMenuItem)
        ;
        $companyMenuItem->expects($this->once())
            ->method('setExtra')
            ->with('description', '管理快递公司信息')
            ->willReturn($companyMenuItem)
        ;
        $numMenuItem->expects($this->once())
            ->method('setExtra')
            ->with('description', '管理物流单号信息')
            ->willReturn($numMenuItem)
        ;
        $statusMenuItem->expects($this->once())
            ->method('setExtra')
            ->with('description', '查看物流状态详情')
            ->willReturn($statusMenuItem)
        ;

        // 确保每个子菜单项都设置了URI（虽然具体值会变化，但调用必须发生）
        $accountMenuItem->method('setUri')->willReturn($accountMenuItem);
        $companyMenuItem->method('setUri')->willReturn($companyMenuItem);
        $numMenuItem->method('setUri')->willReturn($numMenuItem);
        $statusMenuItem->method('setUri')->willReturn($statusMenuItem);

        // 执行测试
        ($adminMenu)($rootItem);
    }

    protected function onSetUp(): void
    {
        $this->item = $this->createMock(ItemInterface::class);

        // 设置 mock 的返回值以避免 null 引用
        $childItem = $this->createMock(ItemInterface::class);
        $this->item->method('addChild')->willReturn($childItem);

        // 使用 willReturnCallback 来模拟 getChild 的行为
        $this->item->method('getChild')->willReturnCallback(function ($name) use ($childItem) {
            return '快递100查询管理' === $name ? $childItem : null;
        });

        // 设置子菜单项的 mock 行为
        $childItem->method('addChild')->willReturn($childItem);
        $childItem->method('setUri')->willReturn($childItem);
        $childItem->method('setAttribute')->willReturn($childItem);
        $childItem->method('setExtra')->willReturn($childItem);
    }
}
