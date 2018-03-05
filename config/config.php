<?php
/**
 * Created by PhpStorm.
 * User: mkunitzsch
 * Date: 01.03.18
 * Time: 11:21
 */


/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['system']['entity_notifyer'] = [
    'tables' => ['tl_entity_notifyer'],
    'icon'   => 'system/modules/entity_notifyer/assets/img/icon.png'
];

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_entity_notifyer'] = 'HeimrichHannot\EntityNotifyer\EntityNotifyerModel';

/**
 * Observer
 */
array_insert(
    $GLOBALS['OBSERVER']['SUBJECTS'],
    0,
    [\HeimrichHannot\EntityNotifyer\Entity::OBSERVER_SUBJECT_ENTITY_NOTIFICATION => 'HeimrichHannot\EntityNotifyer\Observer\NotificationSubject']
);


array_insert(
    $GLOBALS['OBSERVER']['OBSERVERS'],
    0,
    [\HeimrichHannot\EntityNotifyer\Entity::OBSERVER_INACTIVE_ENTITY_NOTIFICATION => 'HeimrichHannot\EntityNotifyer\Observer\EntityInactiveObserver']
);