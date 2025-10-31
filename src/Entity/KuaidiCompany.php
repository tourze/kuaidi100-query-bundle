<?php

namespace Kuaidi100QueryBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Kuaidi100QueryBundle\Repository\KuaidiCompanyRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\Arrayable;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * @implements Arrayable<string, string|null>
 * @implements ApiArrayInterface<string, mixed>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: KuaidiCompanyRepository::class)]
#[ORM\Table(name: 'kuaidi100_company', options: ['comment' => '快递公司编码'])]
class KuaidiCompany implements \Stringable, Arrayable, ApiArrayInterface, AdminArrayInterface
{
    use TimestampableAware;

    #[Groups(groups: ['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(groups: ['admin_curd'])]
    #[Assert\NotBlank(message: 'name字段不能为空')]
    #[Assert\Length(max: 100, maxMessage: 'name字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => '名称'])]
    private ?string $name = null;

    #[Groups(groups: ['admin_curd'])]
    #[Assert\NotBlank(message: 'code字段不能为空')]
    #[Assert\Length(max: 100, maxMessage: 'code字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => '编码'])]
    private ?string $code = null;

    #[Groups(groups: ['admin_curd'])]
    #[Assert\Length(max: 120, maxMessage: 'remark字段长度不能超过{{ limit }}个字符')]
    #[ORM\Column(type: Types::STRING, length: 120, nullable: true, options: ['comment' => 'remark'])]
    private ?string $remark = null;

    public function __toString(): string
    {
        if (null === $this->getId() || 0 === $this->getId()) {
            return '';
        }

        return "{$this->getName()}({$this->getCode()})";
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'code' => $this->getCode(),
            'name' => $this->getName(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveApiArray(): array
    {
        return $this->retrieveAdminArray();
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
            'name' => $this->getName(),
            'code' => $this->getCode(),
            'remark' => $this->getRemark(),
        ];
    }
}
