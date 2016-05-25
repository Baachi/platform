<?php

namespace Oro\Bundle\CalendarBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Oro\Bundle\CalendarBundle\Entity\Calendar;
use Oro\Bundle\CalendarBundle\Entity\CalendarEvent;
use Oro\Bundle\CalendarBundle\Manager\CalendarEventManager;
use Oro\Bundle\SoapBundle\Form\EventListener\PatchSubscriber;

class CalendarEventApiType extends CalendarEventType
{
    /** @var CalendarEventManager */
    protected $calendarEventManager;

    /**
     * @param CalendarEventManager $calendarEventManager
     */
    public function __construct(CalendarEventManager $calendarEventManager)
    {
        $this->calendarEventManager = $calendarEventManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden', array('mapped' => false))
            ->add(
                'calendar',
                'integer',
                [
                    'required' => false,
                    'mapped'   => false
                ]
            )
            ->add(
                'calendarAlias',
                'text',
                [
                    'required' => false,
                    'mapped'   => false
                ]
            )
            ->add('title', 'text', ['required' => true])
            ->add('description', 'text', ['required' => false])
            ->add(
                'start',
                'datetime',
                [
                    'required'       => true,
                    'with_seconds'   => true,
                    'widget'         => 'single_text',
                    'format'         => DateTimeType::HTML5_FORMAT,
                    'model_timezone' => 'UTC'
                ]
            )
            ->add(
                'end',
                'datetime',
                [
                    'required'       => true,
                    'with_seconds'   => true,
                    'widget'         => 'single_text',
                    'format'         => DateTimeType::HTML5_FORMAT,
                    'model_timezone' => 'UTC'
                ]
            )
            ->add('allDay', 'checkbox', ['required' => false])
            ->add('backgroundColor', 'text', ['required' => false])
            ->add('reminders', 'oro_reminder_collection', ['required' => false])
            ->add(
                'invitedUsers',
                'oro_calendar_event_invitees',
                [
                    'required'      => false,
                    'property_path' => 'childEvents'
                ]
            )
            ->add('notifyInvitedUsers', 'hidden', ['mapped' => false])
            ->add(
                'createdAt',
                'datetime',
                [
                    'required'       => false,
                    'with_seconds'   => true,
                    'widget'         => 'single_text',
                    'format'         => DateTimeType::HTML5_FORMAT,
                    'model_timezone' => 'UTC'
                ]
            )
            ->add(
                'recurrence',
                'oro_calendar_event_recurrence',
                [
                    'required' => false,
                ]
            )
            ->add(
                'recurringEventId',
                'oro_entity_identifier',
                [
                    'required' => false,
                    'property_path' => 'recurringEvent',
                    'class' => 'OroCalendarBundle:CalendarEvent',
                    'multiple' => false,
                ]
            )
            ->add(
                'originalStart',
                'datetime',
                [
                    'required' => false,
                    'with_seconds' => true,
                    'widget' => 'single_text',
                    'format' => DateTimeType::HTML5_FORMAT,
                    'model_timezone' => 'UTC',
                ]
            )
            ->add('isCancelled', 'checkbox', ['required' => false]);

        $builder->addEventSubscriber(new PatchSubscriber());
        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'postSubmitData']);
        $this->subscribeOnChildEvents($builder, 'invitedUsers');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'           => 'Oro\Bundle\CalendarBundle\Entity\CalendarEvent',
                'intention'            => 'calendar_event',
                'csrf_protection'      => false,
                'extra_fields_message' => 'This form should not contain extra fields: "{{ extra_fields }}"',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        if (empty($data['recurrence'])) {
            $recurrence = $form->get('recurrence')->getData();
            if ($recurrence) {
                $this->calendarEventManager->removeRecurrence($recurrence);
                $form->get('recurrence')->setData(null);
            }
        }
    }

    /**
     * POST_SUBMIT event handler
     *
     * @param FormEvent $event
     */
    public function postSubmitData(FormEvent $event)
    {
        $form = $event->getForm();

        /** @var CalendarEvent $data */
        $data = $form->getData();
        if (empty($data)) {
            return;
        }

        $calendarId = $form->get('calendar')->getData();
        if (empty($calendarId)) {
            return;
        }
        $calendarAlias = $form->get('calendarAlias')->getData();
        if (empty($calendarAlias)) {
            $calendarAlias = Calendar::CALENDAR_ALIAS;
        }

        $this->calendarEventManager->setCalendar($data, $calendarAlias, (int)$calendarId);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'oro_calendar_event_api';
    }
}
