<?php
/**
 * Created by PhpStorm.
 * User: mkunitzsch
 * Date: 01.03.18
 * Time: 11:21
 */

/**
 * Register the namespaces
 */
ClassLoader::addNamespaces([
    'HeimrichHannot',
]);


/**
 * Register the classes
 */
ClassLoader::addClasses([
    // Classes
    'HeimrichHannot\EntityNotifyer\Entity'                          => 'system/modules/entity_notifyer/classes/Entity.php',
    'HeimrichHannot\EntityNotifyer\Observer\EntityInactiveObserver' => 'system/modules/entity_notifyer/classes/observer/EntityInactiveObserver.php',
    'HeimrichHannot\EntityNotifyer\Observer\NotificationSubject'    => 'system/modules/entity_notifyer/classes/observer/NotificationSubject.php',
    'HeimrichHannot\EntityNotifyer\Backend\EntityNotifyerHelper'    => 'system/modules/entity_notifyer/classes/backend/EntityNotifyerHelper.php',
    
    // Models
    'HeimrichHannot\EntityNotifyer\EntityNotifyerModel'             => 'system/modules/entity_notifyer/models/EntityNotifyerModel.php',
]);
