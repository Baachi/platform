<?php

namespace Oro\Bundle\CalendarBundle\Tests\Functional\API;

use Oro\Bundle\CalendarBundle\Entity\Attendee;
use Oro\Bundle\CalendarBundle\Model\Recurrence;

/**
 * @dbIsolation
 */
class RecurringEventWithAttendeesAndExceptionTest extends AbstractUseCaseTestCase
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testCreateRecurringEventWithAttendeesAndExceptionCanceledDeletedAndUpdateCorrectly()
    {
        $this->checkPreconditions();

        $startDate = '2016-02-07T09:00:00+00:00';
        $endDate = '2016-02-07T09:30:00+00:00';
        $exceptionStart = '2016-02-07T18:00:00+00:00';
        $exceptionEnd = '2016-02-07T18:30:00+00:00';

        $attendeesData = [
            [
                'displayName' => 'simple_user@example.com',
                'email'       => 'simple_user@example.com',
                'status'      => Attendee::STATUS_NONE,
                'type'        => Attendee::TYPE_REQUIRED,
            ],
        ];
        $calendarEventData = [
            'title'       => 'Test Recurring Event',
            'description' => 'Test Recurring Event Description',
            'allDay'      => false,
            'calendar'    => self::DEFAULT_USER_CALENDAR_ID,
            'start'       => $startDate,
            'end'         => $endDate,
            'recurrence'  => [
                'timeZone'       => 'UTC',
                'recurrenceType' => Recurrence::TYPE_WEEKLY,
                'interval'       => 1,
                'dayOfWeek'      => ['saturday'],
                'startTime'      => $startDate,
                'occurrences'    => 5,
                'endTime'        => null,
            ],
            'attendees'   => $attendeesData,
        ];
        $calendarEventId = $this->addCalendarEventViaAPI($calendarEventData);
        $mainCalendarEvent = $this->getCalendarEventById($calendarEventId);

        $exceptionData = $calendarEventData;
        unset($exceptionData['recurrence']);
        $exceptionData['originalStart'] = '2016-02-13T09:00:00+00:00';
        $exceptionData['start'] = $exceptionStart;
        $exceptionData['end'] = $exceptionEnd;
        $exceptionData['recurringEventId'] = $calendarEventId;

        $mainExceptionCalendarEventId = $this->addCalendarEventViaAPI($exceptionData);
        $mainExceptionEvent = $this->getCalendarEventById($mainExceptionCalendarEventId);
        $this->assertCount(1, $mainCalendarEvent->getChildEvents()->toArray());
        $this->assertCount(1, $mainExceptionEvent->getChildEvents()->toArray());

        $simpleUser = $this->getReference('simple_user');

        $expectedEventsData = [
            [
                'id'          => $mainCalendarEvent->getId(),
                'start'       => '2016-03-12T09:00:00+00:00',
                'end'         => '2016-03-12T09:30:00+00:00',
                'title'       => $calendarEventData['title'],
                'description' => $calendarEventData['description'],
                'allDay'      => $calendarEventData['allDay'],
                'isCancelled' => false,
                'attendees'   => [
                    [
                        'userId' => $simpleUser->getId()
                    ]
                ],
            ],
            [
                'id'          => $mainCalendarEvent->getId(),
                'start'       => '2016-03-05T09:00:00+00:00',
                'end'         => '2016-03-05T09:30:00+00:00',
                'title'       => $calendarEventData['title'],
                'description' => $calendarEventData['description'],
                'allDay'      => $calendarEventData['allDay'],
                'isCancelled' => false,
                'attendees'   => [
                    [
                        'userId' => $simpleUser->getId()
                    ]
                ],
            ],
            [
                'id'          => $mainCalendarEvent->getId(),
                'start'       => '2016-02-27T09:00:00+00:00',
                'end'         => '2016-02-27T09:30:00+00:00',
                'title'       => $calendarEventData['title'],
                'description' => $calendarEventData['description'],
                'allDay'      => $calendarEventData['allDay'],
                'isCancelled' => false,
                'attendees'   => [
                    [
                        'userId' => $simpleUser->getId()
                    ]
                ],
            ],
            [
                'id'          => $mainCalendarEvent->getId(),
                'start'       => '2016-02-20T09:00:00+00:00',
                'end'         => '2016-02-20T09:30:00+00:00',
                'title'       => $calendarEventData['title'],
                'description' => $calendarEventData['description'],
                'allDay'      => $calendarEventData['allDay'],
                'isCancelled' => false,
                'attendees'   => [
                    [
                        'userId' => $simpleUser->getId()
                    ]
                ],
            ],
            [
                'id'          => $mainExceptionEvent->getId(),
                'start'       => $exceptionStart,
                'end'         => $exceptionEnd,
                'title'       => $calendarEventData['title'],
                'description' => $calendarEventData['description'],
                'allDay'      => $calendarEventData['allDay'],
                'isCancelled' => false,
                'attendees'   => [
                    [
                        'userId' => $simpleUser->getId()
                    ]
                ],
            ]
        ];
        $actualEvents = $this->getCalendarEventsByCalendarViaAPI(self::DEFAULT_USER_CALENDAR_ID);
        $this->changeExpectedDataCalendarId($expectedEventsData, self::DEFAULT_USER_CALENDAR_ID);
        $this->assertCalendarEvents($expectedEventsData, $actualEvents);

        $simpleUserCalendar = $this->getUserCalendar($simpleUser);

        $attendeeMainEventId = $this->getAttendeeCalendarEvent($simpleUser, $mainCalendarEvent)->getId();
        $expectedEventsData[0]['id'] = $attendeeMainEventId;
        $expectedEventsData[1]['id'] = $attendeeMainEventId;
        $expectedEventsData[2]['id'] = $attendeeMainEventId;
        $expectedEventsData[3]['id'] = $attendeeMainEventId;

        $attendeeExceptionEvent = $this->getAttendeeCalendarEvent($simpleUser, $mainExceptionEvent);
        $expectedEventsData[4]['id'] = $attendeeExceptionEvent->getId();

        $actualEvents = $this->getCalendarEventsByCalendarViaAPI($simpleUserCalendar->getId());
        $this->changeExpectedDataCalendarId($expectedEventsData, $simpleUserCalendar->getId());
        $this->assertCalendarEvents($expectedEventsData, $actualEvents);

        $updatedEventData = $exceptionData;
        $updatedEventData['isCancelled'] = true;
        $this->updateCalendarEventViaAPI($mainExceptionEvent->getId(), $updatedEventData);
        unset($expectedEventsData[4]);

        $actualEvents = $this->getCalendarEventsByCalendarViaAPI($simpleUserCalendar->getId());
        $this->changeExpectedDataCalendarId($expectedEventsData, $simpleUserCalendar->getId());
        $this->assertCalendarEvents($expectedEventsData, $actualEvents);

        $expectedEventsData[0]['id'] = $mainCalendarEvent->getId();
        $expectedEventsData[1]['id'] = $mainCalendarEvent->getId();
        $expectedEventsData[2]['id'] = $mainCalendarEvent->getId();
        $expectedEventsData[3]['id'] = $mainCalendarEvent->getId();
        $actualEvents = $this->getCalendarEventsByCalendarViaAPI(self::DEFAULT_USER_CALENDAR_ID);
        $this->changeExpectedDataCalendarId($expectedEventsData, self::DEFAULT_USER_CALENDAR_ID);
        $this->assertCalendarEvents($expectedEventsData, $actualEvents);

        $mainExceptionEvent = $this->getCalendarEventById($mainExceptionEvent->getId());
        $attendeeExceptionEvent = $this->getCalendarEventById($attendeeExceptionEvent->getId());
        $this->assertTrue($mainExceptionEvent->isCancelled());
        $this->assertTrue($attendeeExceptionEvent->isCancelled());

        $updatedEventData = $calendarEventData;
        $updatedEventData['start'] = '2016-02-07T11:00:00+00:00';
        $updatedEventData['end'] = '2016-02-07T12:00:00+00:00';
        $this->updateCalendarEventViaAPI(
            $mainCalendarEvent->getId(),
            $updatedEventData
        );

        $expectedCalendarEventsUpdatedData = [
            [
                'start'       => '2016-03-12T11:00:00+00:00',
                'end'         => '2016-03-12T12:00:00+00:00',
                'isCancelled' => false
            ],
            [
                'start'       => '2016-03-05T11:00:00+00:00',
                'end'         => '2016-03-05T12:00:00+00:00',
                'isCancelled' => false
            ],
            [
                'start'       => '2016-02-27T11:00:00+00:00',
                'end'         => '2016-02-27T12:00:00+00:00',
                'isCancelled' => false
            ],
            [
                'start'       => '2016-02-20T11:00:00+00:00',
                'end'         => '2016-02-20T12:00:00+00:00',
                'isCancelled' => false
            ],
            [
                'start'       => '2016-02-13T11:00:00+00:00',
                'end'         => '2016-02-13T12:00:00+00:00',
                'isCancelled' => false
            ],
            [
                'start'       => $exceptionStart,
                'end'         => $exceptionEnd,
                'isCancelled' => true
            ]
        ];
        $actualEvents = $this->getCalendarEventsByCalendarViaAPI(self::DEFAULT_USER_CALENDAR_ID);
        $this->changeExpectedDataCalendarId($expectedCalendarEventsUpdatedData, self::DEFAULT_USER_CALENDAR_ID);
        $this->assertCalendarEvents($expectedCalendarEventsUpdatedData, $actualEvents);

        $actualEvents = $this->getCalendarEventsByCalendarViaAPI($simpleUserCalendar->getId());
        $this->changeExpectedDataCalendarId($expectedCalendarEventsUpdatedData, $simpleUserCalendar->getId());
        $this->assertCalendarEvents($expectedCalendarEventsUpdatedData, $actualEvents);

        $this->deleteEventViaAPI($mainExceptionEvent->getId());
        unset($expectedCalendarEventsUpdatedData[5]);

        $this->getEntityManager()->clear();
        $mainExceptionEvent = $this->getCalendarEventById($mainExceptionEvent->getId());
        $attendeeExceptionEvent = $this->getCalendarEventById($attendeeExceptionEvent->getId());
        $this->assertNull($mainExceptionEvent);
        $this->assertNull($attendeeExceptionEvent);
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testCreateRecurringEventWithAttendeesAndExceptionCanceledDeletedAndPartiallyUpdatedCorrectly()
    {
        $this->markTestSkipped('Should be enabled after AEIV-769 STR 6 will be fixed.');

        $this->checkPreconditions();

        $startDate = '2016-02-07T09:00:00+00:00';
        $endDate = '2016-02-07T09:30:00+00:00';
        $exceptionStart = '2016-02-07T18:00:00+00:00';
        $exceptionEnd = '2016-02-07T18:30:00+00:00';

        $attendeesData = [
            [
                'displayName' => 'simple_user@example.com',
                'email'       => 'simple_user@example.com',
                'status'      => Attendee::STATUS_NONE,
                'type'        => Attendee::TYPE_REQUIRED,
            ],
        ];
        $calendarEventData = [
            'title'       => 'Test Recurring Event',
            'description' => 'Test Recurring Event Description',
            'allDay'      => false,
            'calendar'    => self::DEFAULT_USER_CALENDAR_ID,
            'start'       => $startDate,
            'end'         => $endDate,
            'recurrence'  => [
                'timeZone'       => 'UTC',
                'recurrenceType' => Recurrence::TYPE_WEEKLY,
                'interval'       => 1,
                'dayOfWeek'      => ['saturday'],
                'startTime'      => $startDate,
                'occurrences'    => 5,
                'endTime'        => null,
            ],
            'attendees'   => $attendeesData,
        ];
        $calendarEventId = $this->addCalendarEventViaAPI($calendarEventData);
        $mainCalendarEvent = $this->getCalendarEventById($calendarEventId);

        $exceptionData = $calendarEventData;
        unset($exceptionData['recurrence']);
        $exceptionData['originalStart'] = '2016-02-13T09:00:00+00:00';
        $exceptionData['start'] = $exceptionStart;
        $exceptionData['end'] = $exceptionEnd;
        $exceptionData['recurringEventId'] = $calendarEventId;

        $mainExceptionCalendarEventId = $this->addCalendarEventViaAPI($exceptionData);
        $mainExceptionEvent = $this->getCalendarEventById($mainExceptionCalendarEventId);
        $this->assertCount(1, $mainCalendarEvent->getChildEvents()->toArray());
        $this->assertCount(1, $mainExceptionEvent->getChildEvents()->toArray());

        $simpleUser = $this->getReference('simple_user');

        $expectedEventsData = [
            [
                'id'          => $mainCalendarEvent->getId(),
                'start'       => '2016-03-12T09:00:00+00:00',
                'end'         => '2016-03-12T09:30:00+00:00',
                'title'       => $calendarEventData['title'],
                'description' => $calendarEventData['description'],
                'allDay'      => $calendarEventData['allDay'],
                'isCancelled' => false,
                'attendees'   => [
                    [
                        'userId' => $simpleUser->getId()
                    ]
                ],
            ],
            [
                'id'          => $mainCalendarEvent->getId(),
                'start'       => '2016-03-05T09:00:00+00:00',
                'end'         => '2016-03-05T09:30:00+00:00',
                'title'       => $calendarEventData['title'],
                'description' => $calendarEventData['description'],
                'allDay'      => $calendarEventData['allDay'],
                'isCancelled' => false,
                'attendees'   => [
                    [
                        'userId' => $simpleUser->getId()
                    ]
                ],
            ],
            [
                'id'          => $mainCalendarEvent->getId(),
                'start'       => '2016-02-27T09:00:00+00:00',
                'end'         => '2016-02-27T09:30:00+00:00',
                'title'       => $calendarEventData['title'],
                'description' => $calendarEventData['description'],
                'allDay'      => $calendarEventData['allDay'],
                'isCancelled' => false,
                'attendees'   => [
                    [
                        'userId' => $simpleUser->getId()
                    ]
                ],
            ],
            [
                'id'          => $mainCalendarEvent->getId(),
                'start'       => '2016-02-20T09:00:00+00:00',
                'end'         => '2016-02-20T09:30:00+00:00',
                'title'       => $calendarEventData['title'],
                'description' => $calendarEventData['description'],
                'allDay'      => $calendarEventData['allDay'],
                'isCancelled' => false,
                'attendees'   => [
                    [
                        'userId' => $simpleUser->getId()
                    ]
                ],
            ],
            [
                'id'          => $mainExceptionEvent->getId(),
                'start'       => $exceptionStart,
                'end'         => $exceptionEnd,
                'title'       => $calendarEventData['title'],
                'description' => $calendarEventData['description'],
                'allDay'      => $calendarEventData['allDay'],
                'isCancelled' => false,
                'attendees'   => [
                    [
                        'userId' => $simpleUser->getId()
                    ]
                ],
            ]
        ];
        $actualEvents = $this->getCalendarEventsByCalendarViaAPI(self::DEFAULT_USER_CALENDAR_ID);
        $this->changeExpectedDataCalendarId($expectedEventsData, self::DEFAULT_USER_CALENDAR_ID);
        $this->assertCalendarEvents($expectedEventsData, $actualEvents);

        $simpleUserCalendar = $this->getUserCalendar($simpleUser);

        $attendeeMainEventId = $this->getAttendeeCalendarEvent($simpleUser, $mainCalendarEvent)->getId();
        $expectedEventsData[0]['id'] = $attendeeMainEventId;
        $expectedEventsData[1]['id'] = $attendeeMainEventId;
        $expectedEventsData[2]['id'] = $attendeeMainEventId;
        $expectedEventsData[3]['id'] = $attendeeMainEventId;

        $attendeeExceptionEvent = $this->getAttendeeCalendarEvent($simpleUser, $mainExceptionEvent);
        $expectedEventsData[4]['id'] = $attendeeExceptionEvent->getId();

        $actualEvents = $this->getCalendarEventsByCalendarViaAPI($simpleUserCalendar->getId());
        $this->changeExpectedDataCalendarId($expectedEventsData, $simpleUserCalendar->getId());
        $this->assertCalendarEvents($expectedEventsData, $actualEvents);

        $outlookCancelRequest = [
            'isCancelled'      => true,
            'title'            => $exceptionData['title'],
            'description'      => $exceptionData['description'],
            'start'            => $exceptionData['start'],
            'allDay'           => $exceptionData['allDay'],
            'calendar'         => $exceptionData['calendar'],
            'recurringEventId' => $exceptionData['recurringEventId'],
            'originalStart'    => $exceptionData['originalStart'],
            'end'              => $exceptionData['end'],
        ];
        $this->updateCalendarEventViaAPI($mainExceptionEvent->getId(), $outlookCancelRequest);
        unset($expectedEventsData[4]);

        $actualEvents = $this->getCalendarEventsByCalendarViaAPI($simpleUserCalendar->getId());
        $this->changeExpectedDataCalendarId($expectedEventsData, $simpleUserCalendar->getId());
        $this->assertCalendarEvents($expectedEventsData, $actualEvents);

        $expectedEventsData[0]['id'] = $mainCalendarEvent->getId();
        $expectedEventsData[1]['id'] = $mainCalendarEvent->getId();
        $expectedEventsData[2]['id'] = $mainCalendarEvent->getId();
        $expectedEventsData[3]['id'] = $mainCalendarEvent->getId();

        $actualEvents = $this->getCalendarEventsByCalendarViaAPI(self::DEFAULT_USER_CALENDAR_ID);
        $this->changeExpectedDataCalendarId($expectedEventsData, self::DEFAULT_USER_CALENDAR_ID);
        $this->assertCalendarEvents($expectedEventsData, $actualEvents);

        $mainExceptionEvent = $this->getCalendarEventById($mainExceptionEvent->getId());
        $attendeeExceptionEvent = $this->getCalendarEventById($attendeeExceptionEvent->getId());
        $this->assertTrue($mainExceptionEvent->isCancelled());
        $this->assertTrue($attendeeExceptionEvent->isCancelled());

        $this->updateCalendarEventViaAPI(
            $mainCalendarEvent->getId(),
            ['2016-02-07T11:00:00+00:00', '2016-02-07T12:00:00+00:00']
        );

        $expectedCalendarEventsUpdatedData = [
            [
                'start'       => '2016-03-12T11:00:00+00:00',
                'end'         => '2016-03-12T12:00:00+00:00',
                'isCancelled' => false
            ],
            [
                'start'       => '2016-03-05T11:00:00+00:00',
                'end'         => '2016-03-05T12:00:00+00:00',
                'isCancelled' => false
            ],
            [
                'start'       => '2016-02-27T11:00:00+00:00',
                'end'         => '2016-02-27T12:00:00+00:00',
                'isCancelled' => false
            ],
            [
                'start'       => '2016-02-20T11:00:00+00:00',
                'end'         => '2016-02-20T12:00:00+00:00',
                'isCancelled' => false
            ],
            [
                'start'       => '2016-02-13T11:00:00+00:00',
                'end'         => '2016-02-13T12:00:00+00:00',
                'isCancelled' => false
            ],
            [
                'start'       => $exceptionStart,
                'end'         => $exceptionEnd,
                'isCancelled' => true
            ]
        ];
        $actualEvents = $this->getCalendarEventsByCalendarViaAPI(self::DEFAULT_USER_CALENDAR_ID);
        $this->changeExpectedDataCalendarId($expectedCalendarEventsUpdatedData, self::DEFAULT_USER_CALENDAR_ID);
        $this->assertCalendarEvents($expectedCalendarEventsUpdatedData, $actualEvents);

        $this->changeExpectedDataCalendarId($expectedCalendarEventsUpdatedData, $simpleUserCalendar->getId());
        $this->assertCalendarEvents($expectedCalendarEventsUpdatedData, $actualEvents);

        $this->deleteEventViaAPI($mainExceptionEvent->getId());
        unset($expectedCalendarEventsUpdatedData[5]);

        $this->getEntityManager()->clear();
        $mainExceptionEvent = $this->getCalendarEventById($mainExceptionEvent->getId());
        $attendeeExceptionEvent = $this->getCalendarEventById($attendeeExceptionEvent->getId());
        $this->assertNull($mainExceptionEvent);
        $this->assertNull($attendeeExceptionEvent);
    }

    protected function checkPreconditions()
    {
        $result = $this->getCalendarEventsByCalendarViaAPI(self::DEFAULT_USER_CALENDAR_ID);
        $this->assertEmpty($result);
    }

    /**
     * @param int $calendarId
     *
     * @return array
     */
    protected function getCalendarEventsByCalendarViaAPI($calendarId)
    {
        $request = [
            'calendar'    => $calendarId,
            'start'       => '2016-02-06T00:00:00+00:00',
            'end'         => '2016-04-15T00:00:00+00:00',
            'subordinate' => true,
        ];

        return $this->getOrderedCalendarEventsViaAPI($request);
    }
}
