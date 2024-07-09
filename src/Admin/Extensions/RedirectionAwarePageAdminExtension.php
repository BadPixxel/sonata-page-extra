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

namespace BadPixxel\SonataPageExtra\Admin\Extensions;

use BadPixxel\SonataPageExtra\Interfaces\RedirectionAwarePageInterface;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\PageBundle\Model\PageInterface;

/**
 * @template-extends AbstractAdminExtension<PageInterface>
 */
class RedirectionAwarePageAdminExtension extends AbstractAdminExtension
{
    /**
     * @inheritdoc
     */
    public function configureFormFields(FormMapper $form): void
    {
        //==============================================================================
        // Safety Checks - Seo Aware Page
        if (!$this->isRedirectionAwarePageSubject($form->getAdmin()->getModelClass())) {
            return;
        }
        //==============================================================================
        // Add Page Redirections
        $form
            ->end()
            ->with('redirections', array(
                'label' => "SEO Redirections",
            ))
            ->add('redirections', CollectionType::class, array(
                'label' => 'Uri that should redirect to this page',
                'by_reference' => false,

                'required' => false,
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
            ))
            ->end()
        ;
    }

    /**
     * Ensure Subject is Redirection Aware Page
     */
    private function isRedirectionAwarePageSubject(string $subjectClass): bool
    {
        if (!class_exists($subjectClass)) {
            return false;
        }
        if (!is_a($subjectClass, PageInterface::class, true)) {
            return false;
        }
        if (!is_a($subjectClass, RedirectionAwarePageInterface::class, true)) {
            return false;
        }

        return true;
    }
}
