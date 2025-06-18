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
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Action\Listable;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '快递账号')]
#[Listable]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'kuaidi100_account', options: ['comment' => '快递账号'])]
class Account implements \Stringable, Arrayable, AdminArrayInterface
{
    use TimestampableAware;
    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]#[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]#[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'recursive_view', 'api_tree'])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[CreatedByColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[BoolColumn]
    #[IndexColumn]
    #[TrackColumn]
    #[Groups(['admin_curd', 'restful_read', 'restful_read', 'restful_write'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[ListColumn(order: 97)]
    #[FormField(order: 97)]
    private ?bool $valid = false;

    #[Groups(['admin_curd'])]
    #[TrackColumn]
    #[FormField]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => 'customer'])]
    private ?string $customer = null;

    #[Groups(['admin_curd'])]
    #[TrackColumn]
    #[FormField]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => 'userid'])]
    private ?string $userid = null;

    #[Groups(['admin_curd'])]
    #[TrackColumn]
    #[FormField]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => 'secret'])]
    private ?string $secret = null;

    #[Groups(['admin_curd'])]
    #[TrackColumn]
    #[FormField]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '授权key'])]
    private ?string $signKey = null;

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getCustomer()}({$this->getUserid()})";
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
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
