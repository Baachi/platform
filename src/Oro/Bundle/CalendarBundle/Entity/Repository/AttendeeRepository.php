<?php

namespace Oro\Bundle\CalendarBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

use Oro\Bundle\CalendarBundle\Entity\CalendarEvent;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

class AttendeeRepository extends EntityRepository
{
    /**
     * @param Organization|null $organization
     * @param string|null $query
     * @param string[] $excludedNames
     * @param int|null $limit
     *
     * @return array
     */
    public function getEmailRecipients(
        Organization $organization = null,
        $query = null,
        $excludedNames = [],
        $limit = null
    ) {
        $subQb = $this->createQueryBuilder('sa')
            ->select('MIN(sa.id)')
            ->groupBy('sa.email, sa.displayName');

        if ($limit) {
            $subQb->setMaxResults($limit);
        }

        $qb = $this->createQueryBuilder('a');

        if ($query) {
            $subQb
                ->andWhere($subQb->expr()->orX(
                    $subQb->expr()->like('a.displayName', ':query'),
                    $subQb->expr()->like('a.email', ':query')
                ));
            $qb->setParameter('query', sprintf('%%%s%%', $query));
        }

        if ($organization || $excludedNames) {
            $subQb
                ->join('sa.calendarEvent', 'se')
                ->join('se.calendar', 'sc')
                ->join('sc.organization', 'so');
        }

        if ($organization) {
            $subQb
                ->andWhere('so.id = :organization');
            $qb->setParameter('organization', $organization);
        }

        if ($excludedNames) {
            $subQb->andWhere($qb->expr()->notIn(
                'TRIM(CONCAT(sa.displayName, \' <\', sa.email, \'>|\', so.name))',
                ':excluded_names'
            ));
            $qb->setParameter('excluded_names', $excludedNames);
        }

        $qb
            ->select('a.id as entityId, a.email, a.displayName AS name, o.name AS organization')
            ->join('a.calendarEvent', 'e')
            ->join('e.calendar', 'c')
            ->join('c.organization', 'o')
            ->where($qb->expr()->in('a.id', $subQb->getDQL()));

        return $qb->getQuery()
            ->getArrayResult();
    }

    /**
     * @param CalendarEvent|int $calendarEvent
     *
     * @return array
     */
    public function getAttendeeList($calendarEvent)
    {
        return $this->createQueryBuilder('a')
            ->select('a.displayName, a.email, a.createdAt, a.updatedAt, o.id AS origin, s.id as status, t.id as type')
            ->leftJoin('a.origin', 'o')
            ->leftJoin('a.status', 's')
            ->leftJoin('a.type', 't')
            ->where('a.calendarEvent = :calendar_event')
            ->setParameter('calendar_event', $calendarEvent)
            ->getQuery()
            ->getArrayResult();
    }
}
