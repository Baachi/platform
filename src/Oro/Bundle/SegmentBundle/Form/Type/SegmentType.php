<?php

namespace Oro\Bundle\SegmentBundle\Form\Type;

use Oro\Bundle\EntityBundle\Form\Type\EntityFieldSelectType;
use Oro\Bundle\QueryDesignerBundle\Form\Type\AbstractQueryDesignerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SegmentType extends AbstractQueryDesignerType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', ['required' => true])
            ->add('entity', SegmentEntityChoiceType::class, ['required' => true])
            ->add(
                'type',
                EntityType::class,
                [
                    'class'       => 'OroSegmentBundle:SegmentType',
                    'choice_label'    => 'label',
                    'required'    => true,
                    'placeholder' => 'oro.segment.form.choose_segment_type',
                    'tooltip'     => 'oro.segment.type.tooltip_text'
                ]
            )
            ->add('recordsLimit', 'integer', ['required' => false])
            ->add('description', 'textarea', ['required' => false]);

        parent::buildForm($builder, $options);
    }

    /**
     * Gets the default options for this type.
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return [
            'column_column_field_choice_options' => [
                'exclude_fields' => ['relation_type'],
            ],
            'column_column_choice_type' => 'hidden',
            'filter_column_choice_type' => EntityFieldSelectType::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $options = array_merge(
            $this->getDefaultOptions(),
            [
                'data_class'         => 'Oro\Bundle\SegmentBundle\Entity\Segment',
                'csrf_token_id'      => 'segment',
                'query_type'         => 'segment',
            ]
        );

        $resolver->setDefaults($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oro_segment';
    }
}
