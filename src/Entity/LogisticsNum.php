<?php

namespace Kuaidi100QueryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Kuaidi100QueryBundle\Repository\LogisticsNumRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: LogisticsNumRepository::class)]
#[ORM\Table(name: 'kuaidi100_logistics_num', options: ['comment' => '物流单号'])]
class LogisticsNum implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[Assert\NotBlank(message: 'company字段不能为空')]
    #[Assert\Length(max: 20, maxMessage: 'company字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(length: 20, options: ['comment' => '快递公司编码'])]
    private ?string $company = null;

    #[Assert\NotBlank(message: 'number字段不能为空')]
    #[Assert\Length(max: 40, maxMessage: 'number字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(length: 40, unique: true, options: ['comment' => '快递单号'])]
    private ?string $number = null;

    #[Assert\Length(max: 30, maxMessage: 'phoneNumber字段长度不能超过{{ limit }}个字符')]
    #[Assert\Regex(pattern: '/^[\d\+\-\(\)\s]*$/', message: 'phoneNumber字段格式不正确')]
    #[ORM\Column(length: 30, nullable: true, options: ['comment' => '收、寄件人的电话号码'])]
    private ?string $phoneNumber = null;

    #[Assert\Length(max: 120, maxMessage: 'fromCity字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '出发地城市'])]
    private ?string $fromCity = null;

    #[Assert\Length(max: 120, maxMessage: 'toCity字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '目的地城市'])]
    private ?string $toCity = null;

    #[Assert\Length(max: 255, maxMessage: 'latestStatus字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '最近动态'])]
    private ?string $latestStatus = null;

    #[Assert\Type(type: '\DateTimeImmutable', message: 'syncTime字段必须是有效的日期时间')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '上次同步时间'])]
    private ?\DateTimeImmutable $syncTime = null;

    #[Assert\Type(type: 'bool', message: 'subscribed字段必须是布尔值')]
    #[ORM\Column(nullable: true, options: ['comment' => '是否订阅推送'])]
    private ?bool $subscribed = null;

    #[ORM\ManyToOne(inversedBy: 'numbers')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Account $account = null;

    /**
     * @var Collection<int, LogisticsStatus>
     */
    #[ORM\OneToMany(mappedBy: 'number', targetEntity: LogisticsStatus::class)]
    private Collection $statusList;

    public function __construct()
    {
        $this->statusList = new ArrayCollection();
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): void
    {
        $this->number = $number;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getFromCity(): ?string
    {
        return $this->fromCity;
    }

    public function setFromCity(?string $fromCity): void
    {
        $this->fromCity = $fromCity;
    }

    public function getToCity(): ?string
    {
        return $this->toCity;
    }

    public function setToCity(?string $toCity): void
    {
        $this->toCity = $toCity;
    }

    public function getLatestStatus(): ?string
    {
        return $this->latestStatus;
    }

    public function setLatestStatus(?string $latestStatus): void
    {
        $this->latestStatus = $latestStatus;
    }

    public function getSyncTime(): ?\DateTimeImmutable
    {
        return $this->syncTime;
    }

    public function setSyncTime(?\DateTimeImmutable $syncTime): void
    {
        $this->syncTime = $syncTime;
    }

    public function isSubscribed(): ?bool
    {
        return $this->subscribed;
    }

    public function setSubscribed(?bool $subscribed): void
    {
        $this->subscribed = $subscribed;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): void
    {
        $this->account = $account;
    }

    /**
     * @return Collection<int, LogisticsStatus>
     */
    public function getStatusList(): Collection
    {
        return $this->statusList;
    }

    public function addStatusList(LogisticsStatus $statusList): static
    {
        if (!$this->statusList->contains($statusList)) {
            $this->statusList->add($statusList);
            $statusList->setNumber($this);
        }

        return $this;
    }

    public function removeStatusList(LogisticsStatus $statusList): static
    {
        if ($this->statusList->removeElement($statusList)) {
            // set the owning side to null (unless already changed)
            if ($statusList->getNumber() === $this) {
                $statusList->setNumber(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->number ?? $this->id ?? '';
    }
}
