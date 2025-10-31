<?php

declare(strict_types=1);

namespace Kuaidi100QueryBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Kuaidi100QueryBundle\Entity\KuaidiCompany;

#[AdminCrud(
    routePath: '/kuaidi100-query/company',
    routeName: 'kuaidi100_query_company'
)]
final class KuaidiCompanyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return KuaidiCompany::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('快递公司')
            ->setEntityLabelInPlural('快递公司管理')
            ->setPageTitle(Crud::PAGE_INDEX, '快递公司列表')
            ->setPageTitle(Crud::PAGE_NEW, '新建快递公司')
            ->setPageTitle(Crud::PAGE_EDIT, '编辑快递公司')
            ->setPageTitle(Crud::PAGE_DETAIL, '快递公司详情')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['name', 'code'])
            ->showEntityActionsInlined()
            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();

        yield TextField::new('name', '公司名称')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('快递公司中文名称')
        ;

        yield TextField::new('code', '公司编码')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('快递公司唯一编码标识')
        ;

        yield TextField::new('remark', '备注')
            ->setColumns('col-md-12')
            ->setRequired(false)
            ->setMaxLength(120)
            ->setHelp('快递公司备注信息')
            ->hideOnIndex()
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
            ->add(TextFilter::new('name', '公司名称'))
            ->add(TextFilter::new('code', '公司编码'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
