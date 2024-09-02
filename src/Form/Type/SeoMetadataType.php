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

namespace BadPixxel\SonataPageExtra\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SeoMetadataType extends AbstractType
{
    /**
     * Metadata Type Codes.
     *
     * @var string[]
     */
    private static array $types = array(
        'name',
        'property',
        'http-equiv',
    );

    /**
     * Build Metadata Form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //==============================================================================
        //  Metadata Type
        $builder->add('type', ChoiceType::class, array(
            'label' => 'admin.page.metaExtra.type.label',
            'required' => true,
            'choices' => array_combine(
                array_map(fn ($value) => ucfirst($value), self::$types),
                self::$types,
            ),
            'translation_domain' => 'SonataPageExtra',
        ));
        //==============================================================================
        //  Metadata Name
        $builder->add('name', TextType::class, array(
            'label' => 'admin.page.metaExtra.name.label',
            'required' => true,
            'translation_domain' => 'SonataPageExtra',
        ));
        //==============================================================================
        //  Metadata Value
        $builder->add('content', TextType::class, array(
            'label' => 'admin.page.metaExtra.value.label',
            'required' => true,
            'translation_domain' => 'SonataPageExtra',
        ));
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'badpixxel_sonata_page_extra_metadata';
    }
}
