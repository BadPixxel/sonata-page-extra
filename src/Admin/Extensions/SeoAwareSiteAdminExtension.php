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

use BadPixxel\SonataPageExtra\Form\Type\SeoMetadataType;
use BadPixxel\SonataPageExtra\Interfaces\SeoAwareSiteInterface;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\CollectionType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\PageBundle\Model\SiteInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * @template-extends AbstractAdminExtension<SiteInterface>
 */
class SeoAwareSiteAdminExtension extends AbstractAdminExtension
{
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
            ->with('seo_advanced', array('label' => "Advanced SEO Parameters"))
            ->add('robotsExtra', null, array(
                'label' => "admin.site.robotsExtra.label",
                'help' => "admin.site.robotsExtra.help",
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
            ->with('seo_advanced', array(
                'label' => "Advanced SEO Parameters",
                'class' => "col-md-6"
            ))
            ->add('robotsExtra', TextareaType::class, array(
                'label' => "Robots.txt",
                'required' => false,
                'help' => "admin.site.robotsExtra.help",
                'translation_domain' => 'SonataPageExtra',
            ))
            ->add(
                'metaExtra',
                CollectionType::class,
                array(
                    'label' => "Page Additional Metadata",
                    'help' => "admin.page.metaExtra.help",
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_type' => SeoMetadataType::class,
                    'translation_domain' => 'SonataPageExtra',
                )
            )
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
        if (!is_a($subjectClass, SiteInterface::class, true)) {
            return false;
        }
        if (!is_a($subjectClass, SeoAwareSiteInterface::class, true)) {
            return false;
        }

        return true;
    }
}
