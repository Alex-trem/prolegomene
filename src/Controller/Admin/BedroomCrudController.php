<?php

namespace App\Controller\Admin;

use App\Entity\Bedroom;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BedroomCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Bedroom::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
