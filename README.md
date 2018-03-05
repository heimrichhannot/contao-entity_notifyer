# Entity Notifyer

This module is used to send a notification for the subject that an entity hasn't been changed in a while.


## Technical instruction

### set up the entity_notifyer

### Step 1

- create a new observer in heimrichhannot\contao-observer backend module
- select `entity_notification` as subject in the observer config
- select a dataContainer
- select `entity_inactive_notifcation` as observer in the observer config
- set criteria on which the notification should be send

### Step 2

- create a new entity_notification in heimrichhannot\contao-entity_notifyer backend module 
- select an previously defined observer 
- choose a notification that should be send when the criteria set in the observer is fullfilled

### optional

- enable the backend user to exclude an entity from the entity notifyer (e.g. this could come to use when a notification has been send a couple of times for that entity and you don't want to get more)

- add `excludeFromEntityNotifyer` field to dca which you want to exclude from the notification

```
  \HeimrichHannot\EntityNotifyer\Backend\EntityNotifyerHelper::addExcludeField('tl_member');
```
  
- add the `operation` to the dca

```
   $dc['list']['operations'][\HeimrichHannot\EntityNotifyer\Entity::ENTITY_NOTIFYER_EXCLUDE_FROM_OBSERVER] = [
       'label'           => &$GLOBALS['TL_LANG']['OBSERVER'][\HeimrichHannot\EntityNotifyer\Entity::ENTITY_NOTIFYER_EXCLUDE][0],
       'title'           => &$GLOBALS['TL_LANG']['OBSERVER'][\HeimrichHannot\EntityNotifyer\Entity::ENTITY_NOTIFYER_EXCLUDE][1],
       'href'            => 'key=' . \HeimrichHannot\EntityNotifyer\Entity::ENTITY_NOTIFYER_EXCLUDE,
       'icon'            => 'system/modules/entity_observer/assets/img/_icon.png',
       'button_callback' => [
           'HeimrichHannot\EntityObserver\Backend\EntityObserverHelper',
           \HeimrichHannot\EntityNotifyer\Entity::ENTITY_NOTIFYER_EXCLUDE_FROM_OBSERVER
       ]
   ]; 
  ```

- add the operation in the `config.php`

```
// exclude from entity_notifyer
$GLOBALS['BE_MOD']['accounts']['member'][\HeimrichHannot\EntityNotifyer\Entity::ENTITY_NOTIFYER_EXCLUDE] =
    ['HeimrichHannot\EntityNotifyer\Backend\EntityNotifyerHelper', \HeimrichHannot\EntityNotifyer\Entity::ENTITY_NOTIFYER_EXCLUDE];
// include to entity_notifyer
$GLOBALS['BE_MOD']['accounts']['member'][\HeimrichHannot\EntityNotifyer\Entity::ENTITY_NOTIFYER_INCLUDE] =
    ['HeimrichHannot\EntityNotifyer\Backend\EntityNotifyerHelper', \HeimrichHannot\EntityNotifyer\Entity::ENTITY_NOTIFYER_INCLUDE];
```

- you can now exclude/include an entity from the functionality of entity_notifyer at the backend entity


## Classes

Name | Description
---- | -----------
Entity | set up constants
EntityNotifyerHelper | Helper class to get options for dca and handle the exclude/include of entities from entity_notifyer
EntityInactiveObserver | Observer that can be selected in heimrichhannot/contao-observer config. Sends the notification
NotificationSubject | Subject that can be selected in heimrichhannot/contao-observer. Notifys the observer wether it should send the notification. Check if entity is excluded from entity_notifyer happens here.

## Hooks

Name | Arguments | Expected return value | Description
---- | --------- | --------------------- | -----------
editEntityNotificationTokens | $entity,$tokens | $tokens | Modify the tokens used for the notification send by entity_notifyer


