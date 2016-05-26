<?php

namespace Oro\Bundle\CalendarBundle\Tests\Unit\Form\EventListener;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Form\FormEvent;

use Oro\Bundle\CalendarBundle\Entity\Calendar;
use Oro\Bundle\CalendarBundle\Entity\CalendarEvent;
use Oro\Bundle\CalendarBundle\Form\EventListener\ChildEventsSubscriber;
use Oro\Bundle\CalendarBundle\Tests\Unit\Fixtures\Entity\Attendee;
use Oro\Bundle\FilterBundle\Tests\Unit\Filter\Fixtures\TestEnumValue;
use Oro\Bundle\CalendarBundle\Tests\Unit\Fixtures\Entity\User;

class ChildEventsSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /** @var ChildEventsSubscriber */
    protected $childEventsSubscriber;

    public function setUp()
    {
        $formBuilder = $this->getMock('Symfony\Component\Form\FormBuilderInterface');
        $formBuilder->expects($this->any())
            ->method('get')
            ->will($this->returnSelf());

        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->any())
            ->method('find')
            ->will($this->returnCallback(function ($id) {
                return new TestEnumValue($id, $id);
            }));

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValueMap([
                ['Extend\Entity\EV_Oro_Attendee_Status', null, $repository]
            ]));

        $securityFacade = $this->getMockBuilder('Oro\Bundle\SecurityBundle\SecurityFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $this->childEventsSubscriber = new ChildEventsSubscriber(
            $formBuilder,
            $registry,
            $securityFacade
        );
    }

    public function testPreSubmit()
    {
        $calendarEvent = new CalendarEvent();
        $calendarEvent->setTitle('test');

        $form = $this->getMock('Symfony\Component\Form\FormInterface');
        $form->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($calendarEvent));

        $this->assertAttributeEmpty('parentEvent', $this->childEventsSubscriber);
        $this->childEventsSubscriber->preSubmit(new FormEvent($form, []));
        $this->assertAttributeEquals($calendarEvent, 'parentEvent', $this->childEventsSubscriber);
    }

    public function testPostSubmitChildEvents()
    {
        $firstAttendee = new Attendee();
        $firstAttendee->setUser(new User(1));

        $secondAttendee = new Attendee();
        $secondAttendee->setUser(new User(2));

        $firstExistingAttendee = new Attendee();
        $firstExistingAttendee->setUser(new User(1));

        $parentEvent = new CalendarEvent();
        $parentEvent->addAttendee($firstExistingAttendee);
        $parentEvent->addAttendee($secondAttendee);

        $parentForm = $this->getMock('Symfony\Component\Form\FormInterface');
        $parentForm->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($parentEvent));

        $attendees = new ArrayCollection([$firstAttendee, $secondAttendee]);

        $form = $this->getMock('Symfony\Component\Form\FormInterface');
        $form->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($attendees));

        $this->childEventsSubscriber->preSubmit(new FormEvent($parentForm, []));
        $this->childEventsSubscriber->postSubmitChildEvents(new FormEvent($form, []));

        $this->assertCount(2, $attendees);
        $this->assertEquals($firstExistingAttendee, $attendees[0]);
        $this->assertEquals($secondAttendee, $attendees[1]);
    }

    public function testOnSubmit()
    {
        // set default empty data
        $firstEvent = new CalendarEvent();
        $firstEvent->setTitle('1');
        $firstEvent->setRelatedAttendee(new Attendee());
        $secondEvent = new CalendarEvent();
        $secondEvent->setTitle('2');
        $secondEvent->setRelatedAttendee(new Attendee());
        $eventWithoutRelatedAttendee = new CalendarEvent();
        $eventWithoutRelatedAttendee->setTitle('3');

        $parentEvent = new CalendarEvent();
        $parentEvent->setTitle('parent title')
            ->setRelatedAttendee(new Attendee())
            ->setDescription('parent description')
            ->setStart(new \DateTime('now'))
            ->setEnd(new \DateTime('now'))
            ->setAllDay(true);
        $parentEvent->addChildEvent($firstEvent)
            ->addChildEvent($secondEvent)
            ->addChildEvent($eventWithoutRelatedAttendee);

        $form = $this->getMock('Symfony\Component\Form\FormInterface');
        $form->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($parentEvent));

        // assert default data with default status
        $this->childEventsSubscriber->postSubmit(new FormEvent($form, []));

        $this->assertEquals(CalendarEvent::STATUS_ACCEPTED, $parentEvent->getInvitationStatus());
        $this->assertEquals(CalendarEvent::STATUS_NOT_RESPONDED, $firstEvent->getInvitationStatus());
        $this->assertEquals(CalendarEvent::STATUS_NOT_RESPONDED, $secondEvent->getInvitationStatus());
        $this->assertNull($eventWithoutRelatedAttendee->getInvitationStatus());
        $this->assertEventDataEquals($parentEvent, $firstEvent);
        $this->assertEventDataEquals($parentEvent, $secondEvent);
        $this->assertEventDataEquals($parentEvent, $eventWithoutRelatedAttendee);

        // modify data
        $parentEvent->setTitle('modified title')
            ->setDescription('modified description')
            ->setStart(new \DateTime('tomorrow'))
            ->setEnd(new \DateTime('tomorrow'))
            ->setAllDay(false);

        $parentEvent->getRelatedAttendee()->setStatus(
            new TestEnumValue(CalendarEvent::STATUS_ACCEPTED, CalendarEvent::STATUS_ACCEPTED)
        );
        $firstEvent->getRelatedAttendee()->setStatus(
            new TestEnumValue(CalendarEvent::STATUS_DECLINED, CalendarEvent::STATUS_DECLINED)
        );
        $secondEvent->getRelatedAttendee()->setStatus(
            new TestEnumValue(CalendarEvent::STATUS_TENTATIVELY_ACCEPTED, CalendarEvent::STATUS_TENTATIVELY_ACCEPTED)
        );

        // assert modified data
        $this->childEventsSubscriber->postSubmit(new FormEvent($form, []));

        $this->assertEquals(CalendarEvent::STATUS_ACCEPTED, $parentEvent->getInvitationStatus());
        $this->assertEquals(CalendarEvent::STATUS_DECLINED, $firstEvent->getInvitationStatus());
        $this->assertEquals(CalendarEvent::STATUS_TENTATIVELY_ACCEPTED, $secondEvent->getInvitationStatus());
        $this->assertNull($eventWithoutRelatedAttendee->getInvitationStatus());
        $this->assertEventDataEquals($parentEvent, $firstEvent);
        $this->assertEventDataEquals($parentEvent, $secondEvent);
        $this->assertEventDataEquals($parentEvent, $eventWithoutRelatedAttendee);
    }

    public function testRelatedAttendees()
    {
        $user = new User();

        $calendar = (new Calendar())
            ->setOwner($user);

        $attendees = new ArrayCollection([
            (new Attendee())
                ->setUser($user)
        ]);

        $event = (new CalendarEvent())
            ->setAttendees($attendees)
            ->setCalendar($calendar);

        $form = $this->getMock('Symfony\Component\Form\FormInterface');
        $form->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($event));

        $this->childEventsSubscriber->postSubmit(new FormEvent($form, []));

        $this->assertEquals($attendees->first(), $event->getRelatedAttendee());
    }

    /**
     * @param CalendarEvent $expected
     * @param CalendarEvent $actual
     */
    protected function assertEventDataEquals(CalendarEvent $expected, CalendarEvent $actual)
    {
        $this->assertEquals($expected->getTitle(), $actual->getTitle());
        $this->assertEquals($expected->getDescription(), $actual->getDescription());
        $this->assertEquals($expected->getStart(), $actual->getStart());
        $this->assertEquals($expected->getEnd(), $actual->getEnd());
        $this->assertEquals($expected->getAllDay(), $actual->getAllDay());
    }
}
