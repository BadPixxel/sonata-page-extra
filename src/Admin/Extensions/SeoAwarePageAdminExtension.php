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

use BadPixxel\SonataPageExtra\Interfaces\SeoAwarePageInterface;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\PageBundle\Model\PageInterface;

/**
 * @template-extends AbstractAdminExtension<PageInterface>
 */
class SeoAwarePageAdminExtension extends AbstractAdminExtension
{
    /**
     * @inheritdoc
     */
    public function configureListFields(ListMapper $list): void
    {
        //==============================================================================
        // Safety Checks - Seo Aware Page
        if (!$this->isSeoAwarePageSubject($list->getAdmin()->getModelClass())) {
            return;
        }
        //==============================================================================
        // Add Indexed Status Flag
        $list->add('indexed', null, array(
            'label' => "admin.page.indexed.label",
            'translation_domain' => 'SonataPageExtra',
            'editable' => true
        ));
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show): void
    {
        //==============================================================================
        // Safety Checks - Seo Aware Page
        if (!$this->isSeoAwarePageSubject($show->getAdmin()->getModelClass())) {
            return;
        }

        //==============================================================================
        // Add Indexed Status Flag
        $show
            ->end()
            ->with('seo', array('label' => "SEO Parameters"))
            ->add('indexed', null, array(
                'label' => "admin.page.indexed.label",
                'help' => "admin.page.indexed.help",
                'translation_domain' => 'SonataPageExtra',
            ))
            ->end()
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureFormFields(FormMapper $form): void
    {
        //==============================================================================
        // Safety Checks - Seo Aware Page
        if (!$this->isSeoAwarePageSubject($form->getAdmin()->getModelClass())) {
            return;
        }

        //==============================================================================
        // Add Indexed Status Flag
        $form
            ->end()
            ->with('seo', array('label' => "SEO Parameters"))
            ->add('indexed', null, array(
                'label' => "Indexed",
                'help' => "admin.page.indexed.help",
                'translation_domain' => 'SonataPageExtra',
            ))
            ->end()
        ;
    }

    /**
     * Ensure Subject is Seo Aware Page
     */
    private function isSeoAwarePageSubject(string $subjectClass): bool
    {
        if (!class_exists($subjectClass)) {
            return false;
        }
        if (!is_a($subjectClass, PageInterface::class, true)) {
            return false;
        }
        if (!is_a($subjectClass, SeoAwarePageInterface::class, true)) {
            return false;
        }

        return true;
    }
}
