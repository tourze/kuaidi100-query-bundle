<?php

namespace Kuaidi100QueryBundle\Tests\Repository;

use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Repository\LogisticsNumRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * 测试LogisticsNumRepository的基本功能
 *
 * @internal
 */
#[CoversClass(LogisticsNumRepository::class)]
#[RunTestsInSeparateProcesses]
final class LogisticsNumRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function createNewEntity(): object
    {
        $logisticsNum = new LogisticsNum();
        $logisticsNum->setCompany('test_company_' . uniqid());
        $logisticsNum->setNumber('test_number_' . uniqid());

        return $logisticsNum;
    }

    /**
     * @return LogisticsNumRepository
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(LogisticsNumRepository::class);
    }

    public function testRepositoryImplementation(): void
    {
        $repository = self::getService(LogisticsNumRepository::class);
        $this->assertInstanceOf(LogisticsNumRepository::class, $repository);
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
    }

    public function testFindNeedSyncList(): void
    {
        $repository = $this->getRepository();

        // 清理可能存在的数据
        $em = self::getEntityManager();
        $em->createQuery('DELETE FROM ' . LogisticsNum::class)->execute();

        // 创建测试数据
        /** @var LogisticsNum $logisticsNum1 */
        $logisticsNum1 = $this->createNewEntity();
        $logisticsNum1->setSyncTime(null); // 从未同步过的
        $repository->save($logisticsNum1);

        /** @var LogisticsNum $logisticsNum2 */
        $logisticsNum2 = $this->createNewEntity();
        $logisticsNum2->setSyncTime(new \DateTimeImmutable('-32 minutes')); // 超过31分钟的
        $repository->save($logisticsNum2);

        /** @var LogisticsNum $logisticsNum3 */
        $logisticsNum3 = $this->createNewEntity();
        $logisticsNum3->setSyncTime(new \DateTimeImmutable('-30 minutes')); // 未超过31分钟的
        $repository->save($logisticsNum3);

        $now = Carbon::now();
        $needSyncList = $repository->findNeedSyncList($now);

        // 应该包含 logisticsNum1 和 logisticsNum2，但不包含 logisticsNum3
        $this->assertCount(2, $needSyncList, 'findNeedSyncList应该返回2个需要同步的实体');

        $needSyncIds = array_map(function ($entity) {
            return $entity->getId();
        }, $needSyncList);

        $this->assertContains($logisticsNum1->getId(), $needSyncIds);
        $this->assertContains($logisticsNum2->getId(), $needSyncIds);
        $this->assertNotContains($logisticsNum3->getId(), $needSyncIds);
    }

    public function testFindUnsubscribedList(): void
    {
        $repository = $this->getRepository();

        // 清理可能存在的数据
        $em = self::getEntityManager();
        $em->createQuery('DELETE FROM ' . LogisticsNum::class)->execute();

        // 创建测试数据
        /** @var LogisticsNum $logisticsNum1 */
        $logisticsNum1 = $this->createNewEntity();
        $logisticsNum1->setSubscribed(null); // 未订阅的
        $repository->save($logisticsNum1);

        /** @var LogisticsNum $logisticsNum2 */
        $logisticsNum2 = $this->createNewEntity();
        $logisticsNum2->setSubscribed(true); // 已订阅的
        $repository->save($logisticsNum2);

        /** @var LogisticsNum $logisticsNum3 */
        $logisticsNum3 = $this->createNewEntity();
        $logisticsNum3->setSubscribed(false); // 明确设置为false的
        $repository->save($logisticsNum3);

        $unsubscribedList = $repository->findUnsubscribedList();

        // 应该只包含 logisticsNum1（subscribed为null的）
        $this->assertCount(1, $unsubscribedList, 'findUnsubscribedList应该返回1个未订阅的实体');
        $this->assertEquals($logisticsNum1->getId(), $unsubscribedList[0]->getId());
    }
}
