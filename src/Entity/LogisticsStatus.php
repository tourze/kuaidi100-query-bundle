<?php

namespace Kuaidi100QueryBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Kuaidi100QueryBundle\Enum\LogisticsStateEnum;
use Kuaidi100QueryBundle\Repository\LogisticsStatusRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: LogisticsStatusRepository::class)]
#[ORM\Table(name: 'kuaidi100_logistics_status', options: ['comment' => '物流详情表'])]
#[ORM\UniqueConstraint(name: 'kuaidi100_logistics_status_uniq', columns: ['sn', 'flag'])]
class LogisticsStatus implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[Assert\NotBlank(message: 'sn字段不能为空')]
    #[Assert\Length(max: 100, maxMessage: 'sn字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(options: ['comment' => '快递单号'])]
    private ?string $sn = null;

    #[Assert\NotBlank(message: 'companyCode字段不能为空')]
    #[Assert\Length(max: 100, maxMessage: 'companyCode字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(options: ['comment' => '物流公司编码'])]
    private ?string $companyCode = null;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[Assert\NotBlank(message: 'context字段不能为空')]
    #[Assert\Length(max: 255, maxMessage: 'context字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(length: 255, options: ['comment' => '内容'])]
    private ?string $context = null;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[Assert\NotBlank(message: 'ftime字段不能为空')]
    #[Assert\Length(max: 100, maxMessage: 'ftime字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(length: 100, options: ['comment' => '到达时间'])]
    private ?string $ftime = null;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[Assert\Length(max: 255, maxMessage: 'areaCenter字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '当前所在行政区域经纬度'])]
    private ?string $areaCenter = null;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[Assert\NotBlank(message: 'flag字段不能为空')]
    #[Assert\Length(max: 255, maxMessage: 'flag字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(length: 255, options: ['comment' => '唯一标识'])]
    private ?string $flag = null;

    #[Assert\Choice(callback: [LogisticsStateEnum::class, 'cases'], message: '请选择正确的状态值')]
    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, enumType: LogisticsStateEnum::class, options: ['comment' => '标识'])]
    private ?LogisticsStateEnum $state = null;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[Assert\Length(max: 255, maxMessage: 'location字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '当前位置'])]
    private ?string $location = null;

    #[ORM\ManyToOne(inversedBy: 'statusList', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?LogisticsNum $number = null;

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

    public function setSn(string $sn): void
    {
        $this->sn = $sn;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(string $context): void
    {
        $this->context = $context;
    }

    public function getFtime(): ?string
    {
        return $this->ftime;
    }

    public function setFtime(string $ftime): void
    {
        $this->ftime = $ftime;
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

    public function setNumber(?LogisticsNum $number): void
    {
        $this->number = $number;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }

    public function __toString(): string
    {
        return $this->context ?? $this->sn ?? $this->id ?? '';
    }
}
