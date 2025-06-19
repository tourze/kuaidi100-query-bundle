<?php

namespace Kuaidi100QueryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Kuaidi100QueryBundle\Repository\LogisticsNumRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;

#[ORM\Entity(repositoryClass: LogisticsNumRepository::class)]
#[ORM\Table(name: 'kuaidi100_logistics_num', options: ['comment' => '物流单号'])]
class LogisticsNum
{
    use TimestampableAware;
    #[ExportColumn]
    #[Groups(['restful_read', 'admin_curd', 'recursive_view', 'api_tree'])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ORM\Column(length: 20, options: ['comment' => '快递公司编码'])]
    private ?string $company = null;

    #[ORM\Column(length: 40, unique: true, options: ['comment' => '快递单号'])]
    private ?string $number = null;

    #[ORM\Column(length: 30, nullable: true, options: ['comment' => '收、寄件人的电话号码'])]
    private ?string $phone = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '出发地城市'])]
    private ?string $fromCity = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '目的地城市'])]
    private ?string $toCity = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '最近动态'])]
    private ?string $latestStatus = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '上次同步时间'])]
    private ?\DateTimeInterface $syncTime = null;

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

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFromCity(): ?string
    {
        return $this->fromCity;
    }

    public function setFromCity(?string $fromCity): static
    {
        $this->fromCity = $fromCity;

        return $this;
    }

    public function getToCity(): ?string
    {
        return $this->toCity;
    }

    public function setToCity(?string $toCity): static
    {
        $this->toCity = $toCity;

        return $this;
    }

    public function getLatestStatus(): ?string
    {
        return $this->latestStatus;
    }

    public function setLatestStatus(?string $latestStatus): static
    {
        $this->latestStatus = $latestStatus;

        return $this;
    }

    public function getSyncTime(): ?\DateTimeInterface
    {
        return $this->syncTime;
    }

    public function setSyncTime(?\DateTimeInterface $syncTime): static
    {
        $this->syncTime = $syncTime;

        return $this;
    }

    public function isSubscribed(): ?bool
    {
        return $this->subscribed;
    }

    public function setSubscribed(?bool $subscribed): static
    {
        $this->subscribed = $subscribed;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): static
    {
        $this->account = $account;

        return $this;
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
}
