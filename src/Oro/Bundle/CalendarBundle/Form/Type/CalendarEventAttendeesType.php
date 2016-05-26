<?php

namespace Oro\Bundle\CalendarBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Bundle\CalendarBundle\Entity\Attendee;
use Oro\Bundle\CalendarBundle\Form\DataTransformer\UsersToAttendeesTransformer;
use Oro\Bundle\FormBundle\Autocomplete\ConverterInterface;
use Oro\Bundle\UserBundle\Entity\User;

class CalendarEventAttendeesType extends AbstractType
{
    /** @var ManagerRegistry */
    protected $registry;

    /** @var UsersToAttendeesTransformer */
    protected $usersToAttendeesTransformer;

    /**
     * @param UsersToAttendeesTransformer $usersToAttendeesTransformer
     */
    public function __construct(UsersToAttendeesTransformer $usersToAttendeesTransformer)
    {
        $this->usersToAttendeesTransformer = $usersToAttendeesTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->resetModelTransformers();
        $builder->addModelTransformer($this->usersToAttendeesTransformer);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();
            if (!$data) {
                return;
            }

            $invalidAttendees = array_diff(
                $this->parseNewValues($data),
                $this->parseNewValues($form->getViewData())
            );

            if (!$invalidAttendees) {
                return;
            }

            $form->addError(new FormError(sprintf(
                'This field has invalid attendees: "%s"',
                implode(
                    ', ',
                    array_map(
                        function ($value) {
                            return json_decode($value)->value;
                        },
                        $invalidAttendees
                    )
                )
            )));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'autocomplete_alias' => 'organization_users',
            'configs' => function (Options $options, $value) {
                return array_merge(
                    $value,
                    [
                        'renderedPropertyName' => 'email',
                        'forceSelectedData' => true,
                    ]
                );
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var ConverterInterface $converter */
        $converter = $options['converter'];

        $formData = $form->getData();
        if ($formData) {
            $transformedData = $this->usersToAttendeesTransformer->attendeesToUsers($formData);

            $result = [];
            foreach ($transformedData as $k => $item) {
                $converted = $converter->convertItem($item);

                if (!$this->isAttendeeRemovable($formData[$k], $options['disable_user_removal'])) {
                    $converted['locked'] = true;
                }

                $result[] = $converted;
            }

            $view->vars['attr']['data-selected-data'] = json_encode($result);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'oro_user_multiselect';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'oro_calendar_event_attendees';
    }

    /**
     * @param Attendee $attendee
     * @param bool     $disableUserRemoval
     *
     * @return bool
     */
    protected function isAttendeeRemovable(Attendee $attendee, $disableUserRemoval = false)
    {
        $user          = $attendee->getUser();
        $calendarEvent = $attendee->getCalendarEvent();

        return (
            $user instanceof User
            && !$disableUserRemoval
            && (
                $attendee->getId() !== $calendarEvent->getRelatedAttendee()->getId()
                && $attendee->getId() !== $calendarEvent->getRealCalendarEvent()->getRelatedAttendee()->getId()
            )
        );
    }

    /**
     * @param string $value
     *
     * @return array
     */
    protected function parseNewValues($value)
    {
        return array_filter(
            explode(',', $value),
            function ($value) {
                return !is_numeric($value);
            }
        );
    }
}
