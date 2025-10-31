<?php

namespace Kuaidi100QueryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Kuaidi100QueryBundle\Repository\AccountRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\Arrayable;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * @implements Arrayable<string, string|null>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'kuaidi100_account', options: ['comment' => '快递账号'])]
class Account implements \Stringable, Arrayable, AdminArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    use SnowflakeKeyAware;

    #[IndexColumn]
    #[TrackColumn]
    #[Groups(groups: ['admin_curd', 'restful_read', 'restful_read', 'restful_write'])]
    #[Assert\NotNull(message: 'valid字段不能为空')]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[Groups(groups: ['admin_curd'])]
    #[TrackColumn]
    #[Assert\NotBlank(message: 'customer字段不能为空')]
    #[Assert\Length(max: 100, maxMessage: 'customer字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => 'customer'])]
    private ?string $customer = null;

    #[Groups(groups: ['admin_curd'])]
    #[TrackColumn]
    #[Assert\NotBlank(message: 'userid字段不能为空')]
    #[Assert\Length(max: 100, maxMessage: 'userid字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => 'userid'])]
    private ?string $userid = null;

    #[Groups(groups: ['admin_curd'])]
    #[TrackColumn]
    #[Assert\NotBlank(message: 'secret字段不能为空')]
    #[Assert\Length(max: 120, maxMessage: 'secret字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => 'secret'])]
    private ?string $secret = null;

    #[Groups(groups: ['admin_curd'])]
    #[TrackColumn]
    #[Assert\NotBlank(message: 'signKey字段不能为空')]
    #[Assert\Length(max: 120, maxMessage: 'signKey字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '授权key'])]
    private ?string $signKey = null;

    /**
     * @var Collection<int, LogisticsNum>
     */
    #[ORM\OneToMany(mappedBy: 'account', targetEntity: LogisticsNum::class)]
    private Collection $numbers;

    public function __construct()
    {
        $this->numbers = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (null === $this->getId()) {
            return '';
        }

        return "{$this->getCustomer()}({$this->getUserid()})";
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getUserid(): ?string
    {
        return $this->userid;
    }

    public function setUserid(?string $userid): void
    {
        $this->userid = $userid;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setSecret(?string $secret): void
    {
        $this->secret = $secret;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function setCustomer(?string $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'userid' => $this->getUserid(),
            'secret' => $this->getSecret(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->isValid(),
            'customer' => $this->getCustomer(),
            'userid' => $this->getUserid(),
            'secret' => $this->getSecret(),
            'signKey' => $this->getSignKey(),
        ];
    }

    public function getSignKey(): ?string
    {
        return $this->signKey;
    }

    public function setSignKey(?string $signKey): void
    {
        $this->signKey = $signKey;
    }

    /**
     * @return Collection<int, LogisticsNum>
     */
    public function getNumbers(): Collection
    {
        return $this->numbers;
    }

    public function addNumber(LogisticsNum $number): self
    {
        if (!$this->numbers->contains($number)) {
            $this->numbers->add($number);
            $number->setAccount($this);
        }

        return $this;
    }

    public function removeNumber(LogisticsNum $number): self
    {
        if ($this->numbers->removeElement($number)) {
            if ($number->getAccount() === $this) {
                $number->setAccount(null);
            }
        }

        return $this;
    }
}
