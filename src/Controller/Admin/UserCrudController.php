<?php

namespace App\Controller\Admin;

use App\Entity\Group;
use App\Entity\User;
use App\Form\GroupType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Students')
            ->setEntityLabelInPlural('Students')
            ->showEntityActionsInlined('true')
            ->setSearchFields(['name', 'first_name', 'last_name', 'birthday']);
    }


    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name');
        yield TextField::new('first_name');
        yield TextField::new('last_name');
        yield EmailField::new('email');
        yield DateField::new('birthday');
        yield AssociationField::new('groups');
    }

}
