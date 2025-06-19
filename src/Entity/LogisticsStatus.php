<?php

namespace Kuaidi100QueryBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Kuaidi100QueryBundle\Enum\LogisticsStateEnum;
use Kuaidi100QueryBundle\Repository\LogisticsStatusRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: LogisticsStatusRepository::class)]
#[ORM\Table(name: 'kuaidi100_logistics_status', options: ['comment' => '物流详情表'])]
#[ORM\UniqueConstraint(name: 'kuaidi100_logistics_status_uniq', columns: ['sn', 'flag'])]
class LogisticsStatus implements \Stringable
{
    use TimestampableAware;
    #[Groups(['restful_read', 'admin_curd', 'recursive_view', 'api_tree'])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ORM\Column(options: ['comment' => '快递单号'])]
    private ?string $sn = null;

    #[ORM\Column(options: ['comment' => '物流公司编码'])]
    private ?string $companyCode = null;

    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(length: 255, options: ['comment' => '内容'])]
    private ?string $context = null;

    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(length: 100, options: ['comment' => '到达时间'])]
    private ?string $ftime = null;

    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '当前所在行政区域经纬度'])]
    private ?string $areaCenter = null;

    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(length: 255, options: ['comment' => '唯一标识'])]
    private ?string $flag = null;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, enumType: LogisticsStateEnum::class, options: ['comment' => '标识'])]
    private ?LogisticsStateEnum $state = null;

    #[ORM\ManyToOne(inversedBy: 'statusList')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LogisticsNum $number = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getState(): ?LogisticsStateEnum
    {
        return $this->state;
    }

    public function setState(?LogisticsStateEnum $state): void
    {
        $this->state = $state;
    }

    public function getSn(): ?string
    {
        return $this->sn;
    }

    public function setSn(string $sn): self
    {
        $this->sn = $sn;

        return $this;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getFtime(): ?string
    {
        return $this->ftime;
    }

    public function setFtime(string $ftime): self
    {
        $this->ftime = $ftime;

        return $this;
    }

    public function getCompanyCode(): ?string
    {
        return $this->companyCode;
    }

    public function setCompanyCode(?string $companyCode): void
    {
        $this->companyCode = $companyCode;
    }

    public function getAreaCenter(): ?string
    {
        return $this->areaCenter;
    }

    public function setAreaCenter(?string $areaCenter): void
    {
        $this->areaCenter = $areaCenter;
    }

    public function getFlag(): ?string
    {
        return $this->flag;
    }

    public function setFlag(?string $flag): void
    {
        $this->flag = $flag;
    }

    public function getNumber(): ?LogisticsNum
    {
        return $this->number;
    }

    public function setNumber(?LogisticsNum $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function __toString(): string
    {
        return $this->context ?? $this->sn ?? $this->id ?? '';
    }
}
