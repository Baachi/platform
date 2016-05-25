<?php

namespace Oro\Bundle\CalendarBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use Oro\Bundle\CalendarBundle\Entity\CalendarEvent;
use Oro\Bundle\CalendarBundle\Exception\CalendarEventRelatedAttendeeNotFoundException;
use Oro\Bundle\CalendarBundle\Exception\StatusNotFoundException;
use Oro\Bundle\CalendarBundle\Manager\AttendeeManager;

/**
 * @Route("/event/ajax")
 */
class AjaxCalendarEventController extends Controller
{
    /**
     * @Route("/accept/{id}",
     *      name="oro_calendar_event_accepted",
     *      requirements={"id"="\d+"}, defaults={"status"="accepted"})
     * @Route("/tentatively/{id}",
     *      name="oro_calendar_event_tentatively_accepted",
     *      requirements={"id"="\d+"}, defaults={"status"="tentative"})
     * @Route("/tentatively/{id}",
     *      name="oro_calendar_event_tentative",
     *      requirements={"id"="\d+"}, defaults={"status"="tentative"})
     * @Route("/decline/{id}",
     *      name="oro_calendar_event_declined",
     *      requirements={"id"="\d+"}, defaults={"status"="declined"})
     *
     * @param CalendarEvent $entity
     * @param string        $status
     *
     * @return JsonResponse
     */
    public function changeStatus(CalendarEvent $entity, $status)
    {
        try {
            $this->get('oro_calendar.calendar_event_manager')->changeStatus($entity, $status);
        } catch (CalendarEventRelatedAttendeeNotFoundException $ex) {
            return new JsonResponse([
                'successfull' => false,
                'message' => $ex->getMessage(),
            ]);
        } catch (StatusNotFoundException $ex) {
            return new JsonResponse([
                'successfull' => false,
                'message' => $ex->getMessage(),
            ]);
        }

        $this->getDoctrine()
            ->getManagerForClass('Oro\Bundle\CalendarBundle\Entity\CalendarEvent')
            ->flush();

        $this->get('oro_calendar.send_processor.email')->sendRespondNotification($entity);

        return new JsonResponse(['successful' => true]);
    }

    /**
     * @Route(
     *      "/attendees-autocomplete-data/{id}",
     *      name="oro_calendar_event_attendees_autocomplete_data",
     *      options={"expose"=true}
     * )
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function attendeesAutocompleteDataAction($id)
    {
        $data = $this->get('oro_calendar.attendees_to_view_transformer')
            ->transform($this->getAttendeeManager()->loadAttendeesByCalendarEventId($id));

        $result = array_map(
            function ($data) {
                return json_decode($data, true);
            },
            explode(';', $data)
        );

        return new JsonResponse($result);
    }

    /**
     * @return AttendeeManager
     */
    protected function getAttendeeManager()
    {
        return $this->get('oro_calendar.attendee_manager');
    }
}
