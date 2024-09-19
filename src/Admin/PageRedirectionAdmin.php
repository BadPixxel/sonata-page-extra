<?php

/*
 *  Copyright (C) BadPixxel <www.badpixxel.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace BadPixxel\SonataPageExtra\Admin;

use BadPixxel\SonataPageExtra\Dictionary\RedirectTypes;
use BadPixxel\SonataPageExtra\Entity\PageRedirection;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\DependencyInjection\Admin\TaggedAdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Sonata Page Redirections Admin Class.
 */
#[AutoconfigureTag(TaggedAdminInterface::ADMIN_TAG, array(
    'manager_type' => "orm",
    'show_in_dashboard' => false,
    'model_class' => PageRedirection::class
))]
class PageRedirectionAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $form): void
    {
        /** @var null|PageRedirection $subject */
        $subject = $form->getAdmin()->getSubject();

        //==============================================================================
        //  Redirection Uri
        $form->add('uri', TextType::class, array(
            'label' => 'admin.page.redirection.uri.label',
            'required' => true,
            'row_attr' => array(
                'class' => 'col-md-8',
            ),
            'help' => $subject?->getUri(),
            'translation_domain' => 'SonataPageExtra',
        ));
        //==============================================================================
        //  Redirection Type
        $form->add('code', ChoiceType::class, array(
            'label' => 'admin.page.redirection.type.label',
            'required' => true,
            'choices' => RedirectTypes::ALL,
            'row_attr' => array(
                'class' => 'col-md-4',
            ),
            'translation_domain' => 'SonataPageExtra',
        ));
    }
}
