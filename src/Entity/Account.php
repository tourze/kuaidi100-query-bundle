<?php

namespace Kuaidi100QueryBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Kuaidi100QueryBundle\Repository\AccountRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\Arrayable;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

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
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[Groups(groups: ['admin_curd'])]
    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => 'customer'])]
    private ?string $customer = null;

    #[Groups(groups: ['admin_curd'])]
    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => 'userid'])]
    private ?string $userid = null;

    #[Groups(groups: ['admin_curd'])]
    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => 'secret'])]
    private ?string $secret = null;

    #[Groups(groups: ['admin_curd'])]
    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '授权key'])]
    private ?string $signKey = null;

    public function __toString(): string
    {
        if ($this->getId() === null) {
            return '';
        }

        return "{$this->getCustomer()}({$this->getUserid()})";
    }



    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getUserid(): ?string
    {
        return $this->userid;
    }

    public function setUserid(string $userid): self
    {
        $this->userid = $userid;

        return $this;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setSecret(string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function setCustomer(string $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'userid' => $this->getUserid(),
            'secret' => $this->getSecret(),
        ];
    }

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
}
