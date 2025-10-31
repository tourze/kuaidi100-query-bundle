<?php

declare(strict_types=1);

namespace Kuaidi100QueryBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Kuaidi100QueryBundle\Entity\Account;

#[AdminCrud(
    routePath: '/kuaidi100-query/account',
    routeName: 'kuaidi100_query_account'
)]
final class AccountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('快递账号')
            ->setEntityLabelInPlural('快递账号管理')
            ->setPageTitle(Crud::PAGE_INDEX, '快递账号列表')
            ->setPageTitle(Crud::PAGE_NEW, '新建快递账号')
            ->setPageTitle(Crud::PAGE_EDIT, '编辑快递账号')
            ->setPageTitle(Crud::PAGE_DETAIL, '快递账号详情')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['customer', 'userid'])
            ->showEntityActionsInlined()
            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();

        yield BooleanField::new('valid', '是否有效')
            ->setHelp('是否启用该快递账号')
        ;

        yield TextField::new('customer', '客户标识')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('快递100客户标识')
        ;

        yield TextField::new('userid', '用户ID')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('快递100用户ID')
        ;

        yield TextField::new('secret', '密钥')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setMaxLength(120)
            ->setHelp('快递100接口密钥')
            ->hideOnIndex() // 在列表中隐藏敏感信息
        ;

        yield TextField::new('signKey', '授权Key')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setMaxLength(120)
            ->setHelp('快递100授权key')
            ->hideOnIndex() // 在列表中隐藏敏感信息
        ;

        yield AssociationField::new('numbers', '物流单号')
            ->setColumns('col-md-12')
            ->hideOnForm() // 在表单中隐藏，通过关联管理
            ->setHelp('关联的物流单号列表')
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
            ->add(BooleanFilter::new('valid', '是否有效'))
            ->add(TextFilter::new('customer', '客户标识'))
            ->add(TextFilter::new('userid', '用户ID'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
