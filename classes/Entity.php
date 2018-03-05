<?php
/**
 * Created by PhpStorm.
 * User: mkunitzsch
 * Date: 01.03.18
 * Time: 14:39
 */

namespace HeimrichHannot\EntityNotifyer;


class Entity
{
    const ENTITY_NOTIFYER_EXCLUDE               = 'doExcludeFromEntityNotifyer';
    const ENTITY_NOTIFYER_INCLUDE               = 'includeEntityToEntityNotifyer';
    const ENTITY_NOTIFYER_EXCLUDE_FROM_OBSERVER = 'excludeFromEntityNotifyer';
    
    /**
     * subject
     */
    const OBSERVER_SUBJECT_ENTITY_NOTIFICATION = 'entity_notification';
    
    /**
     * observer
     */
    const OBSERVER_INACTIVE_ENTITY_NOTIFICATION = 'entity_inactive_notification';
}