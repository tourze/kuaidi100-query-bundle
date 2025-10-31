<?php

declare(strict_types=1);

namespace Kuaidi100QueryBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Kuaidi100QueryBundle\Entity\LogisticsStatus;
use Kuaidi100QueryBundle\Enum\LogisticsStateEnum;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

#[AdminCrud(
    routePath: '/kuaidi100-query/logistics-status',
    routeName: 'kuaidi100_query_logistics_status'
)]
final class LogisticsStatusCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LogisticsStatus::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('物流状态')
            ->setEntityLabelInPlural('物流状态管理')
            ->setPageTitle(Crud::PAGE_INDEX, '物流状态列表')
            ->setPageTitle(Crud::PAGE_NEW, '新建物流状态')
            ->setPageTitle(Crud::PAGE_EDIT, '编辑物流状态')
            ->setPageTitle(Crud::PAGE_DETAIL, '物流状态详情')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['sn', 'companyCode', 'context', 'location'])
            ->showEntityActionsInlined()
            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();

        yield TextField::new('sn', '快递单号')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('快递单号')
        ;

        yield TextField::new('companyCode', '物流公司编码')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('物流公司的编码')
        ;

        yield TextField::new('context', '内容')
            ->setColumns('col-md-12')
            ->setRequired(true)
            ->setMaxLength(255)
            ->setHelp('物流状态描述内容')
        ;

        yield TextField::new('ftime', '到达时间')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('包裹到达时间')
        ;

        yield TextField::new('location', '当前位置')
            ->setColumns('col-md-6')
            ->setRequired(false)
            ->setMaxLength(255)
            ->setHelp('包裹当前所在位置')
        ;

        yield TextField::new('areaCenter', '区域经纬度')
            ->setColumns('col-md-6')
            ->setRequired(false)
            ->setMaxLength(255)
            ->setHelp('当前所在行政区域经纬度')
            ->hideOnIndex()
        ;

        yield TextField::new('flag', '唯一标识')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setMaxLength(255)
            ->setHelp('物流状态的唯一标识')
            ->hideOnIndex()
        ;

        $field = EnumField::new('state', '状态标识');
        $field->setEnumCases(LogisticsStateEnum::cases());
        $field->setColumns('col-md-6')
            ->setRequired(false)
            ->setHelp('物流状态枚举')
        ;
        yield $field;

        yield AssociationField::new('number', '关联物流单号')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setHelp('关联的物流单号记录')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->hideOnForm()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->hideOnForm()
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('sn', '快递单号'))
            ->add(TextFilter::new('companyCode', '物流公司编码'))
            ->add(TextFilter::new('context', '内容'))
            ->add(TextFilter::new('location', '当前位置'))
            ->add(EntityFilter::new('number', '关联物流单号'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
