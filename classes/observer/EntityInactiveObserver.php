<?php
/**
 * Created by PhpStorm.
 * User: mkunitzsch
 * Date: 01.03.18
 * Time: 11:00
 */

namespace HeimrichHannot\EntityNotifyer\Observer;



use HeimrichHannot\EntityNotifyer\EntityNotifyerModel;
use HeimrichHannot\Observer\Observer;
use NotificationCenter\Model\Notification;

class EntityInactiveObserver extends Observer
{
    
    protected function doUpdate()
    {
        $entityObserver = EntityNotifyerModel::findByObserver($this->getSubject()->getObserver()->id);
    
        if(null === ($notification = Notification::findByPk($entityObserver->notification)))
        {
            return;
        }
        
        $tokens = [];
        array_walk($this->objSubject->getContext()->row(), function ($val, $key) use (&$tokens) {
            $tokens['form_' . $key] = htmlspecialchars($val);
        });
    
        if (isset($GLOBALS['TL_HOOKS']['editEntityNotificationTokens']) && is_array($GLOBALS['TL_HOOKS']['editEntityNotificationTokens'])) {
            foreach ($GLOBALS['TL_HOOKS']['editEntityNotificationTokens'] as $arrCallback) {
                $tokens = \System::importStatic($arrCallback[0])->{$arrCallback[1]}($this->objSubject->getContext(), $tokens);
            }
        }
    
        $notification->send($tokens, $GLOBALS['TL_LANGUAGE']);
    }
    
    protected function getEntityId()
    {
        return sprintf('%s:%s',  $this->getSubject()->getObserver()->dataContainer,$this->getSubject()->getContext()->id);
    }
}