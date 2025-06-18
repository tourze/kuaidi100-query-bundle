<?php

namespace Kuaidi100QueryBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Kuaidi100QueryBundle\Repository\KuaidiCompanyRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\Arrayable;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Action\Listable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Filter\Keyword;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '快递公司编码')]
#[Listable]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Entity(repositoryClass: KuaidiCompanyRepository::class)]
#[ORM\Table(name: 'kuaidi100_company', options: ['comment' => '快递公司编码'])]
class KuaidiCompany implements \Stringable, Arrayable, ApiArrayInterface, AdminArrayInterface
{
    use TimestampableAware;
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[Groups(['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }
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
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]#[Groups(['admin_curd'])]
    #[FormField(span: 18)]
    #[Keyword]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => '名称'])]
    private ?string $name = null;

    #[Groups(['admin_curd'])]
    #[FormField(span: 10)]
    #[Keyword]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => '编码'])]
    private ?string $code = null;

    #[Groups(['admin_curd'])]
    #[FormField(span: 14)]
    #[Keyword]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 120, nullable: true, options: ['comment' => 'remark'])]
    private ?string $remark = null;

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getName()}({$this->getCode()})";
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(string $remark): self
    {
        $this->remark = $remark;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->getCode(),
            'name' => $this->getName(),
        ];
    }

    public function retrieveApiArray(): array
    {
        return $this->retrieveAdminArray();
    }

    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'name' => $this->getName(),
            'code' => $this->getCode(),
            'remark' => $this->getRemark(),
        ];
    }
}
