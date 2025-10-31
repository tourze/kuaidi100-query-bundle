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
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Kuaidi100QueryBundle\Entity\LogisticsNum;

#[AdminCrud(
    routePath: '/kuaidi100-query/logistics-num',
    routeName: 'kuaidi100_query_logistics_num'
)]
final class LogisticsNumCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LogisticsNum::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('物流单号')
            ->setEntityLabelInPlural('物流单号管理')
            ->setPageTitle(Crud::PAGE_INDEX, '物流单号列表')
            ->setPageTitle(Crud::PAGE_NEW, '新建物流单号')
            ->setPageTitle(Crud::PAGE_EDIT, '编辑物流单号')
            ->setPageTitle(Crud::PAGE_DETAIL, '物流单号详情')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['number', 'company', 'phoneNumber'])
            ->showEntityActionsInlined()
            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();

        yield TextField::new('company', '快递公司编码')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setMaxLength(20)
            ->setHelp('快递公司的编码标识')
        ;

        yield TextField::new('number', '快递单号')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setMaxLength(40)
            ->setHelp('快递单号，系统唯一')
        ;

        yield TextField::new('phoneNumber', '电话号码')
            ->setColumns('col-md-6')
            ->setRequired(false)
            ->setMaxLength(30)
            ->setHelp('收、寄件人的电话号码')
        ;

        yield TextField::new('fromCity', '出发地城市')
            ->setColumns('col-md-6')
            ->setRequired(false)
            ->setMaxLength(120)
            ->setHelp('包裹出发地城市')
        ;

        yield TextField::new('toCity', '目的地城市')
            ->setColumns('col-md-6')
            ->setRequired(false)
            ->setMaxLength(120)
            ->setHelp('包裹目的地城市')
        ;

        yield TextField::new('latestStatus', '最近动态')
            ->setColumns('col-md-12')
            ->setRequired(false)
            ->setMaxLength(255)
            ->setHelp('最近的物流状态更新')
            ->hideOnIndex()
        ;

        yield AssociationField::new('account', '关联账号')
            ->setColumns('col-md-6')
            ->setRequired(false)
            ->setHelp('关联的快递账号')
        ;

        yield BooleanField::new('subscribed', '是否订阅推送')
            ->setHelp('是否订阅物流状态推送')
        ;

        yield DateTimeField::new('syncTime', '上次同步时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setColumns('col-md-6')
            ->hideOnForm()
        ;

        yield AssociationField::new('statusList', '物流状态')
            ->setColumns('col-md-12')
            ->hideOnForm()
            ->setHelp('该单号的所有物流状态记录')
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
            ->add(TextFilter::new('company', '快递公司编码'))
            ->add(TextFilter::new('number', '快递单号'))
            ->add(TextFilter::new('phoneNumber', '电话号码'))
            ->add(TextFilter::new('fromCity', '出发地城市'))
            ->add(TextFilter::new('toCity', '目的地城市'))
            ->add(EntityFilter::new('account', '关联账号'))
            ->add(BooleanFilter::new('subscribed', '是否订阅推送'))
            ->add(DateTimeFilter::new('syncTime', '上次同步时间'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
