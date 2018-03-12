<?php
/**
 * Created by PhpStorm.
 * User: mkunitzsch
 * Date: 01.03.18
 * Time: 14:35
 */

namespace HeimrichHannot\EntityNotifyer\Observer;


use HeimrichHannot\EntityNotifyer\Entity;
use HeimrichHannot\EntityNotifyer\EntityNotifyerModel;
use HeimrichHannot\EntityNotifyer\EntityObserverModel;
use HeimrichHannot\Observer\Backend\ObserverLog;
use HeimrichHannot\Observer\Subject;

class NotificationSubject extends Subject
{
    /**
     * Run your subject observer
     *
     * @return bool True on success, false on error
     */
    public function notify()
    {
        if(null == ($entityObserver = EntityNotifyerModel::findByObserver($this->getObserver()->id)))
        {
            if ($this->getObserver()->debug)
            {
                ObserverLog::add($this->getObserver()->id, 'No tasks for given filter found.', __CLASS__ . ':' . __METHOD__);
            }
    
            return;
        }
        
        if ($this->getObserver()->debug)
        {
            $count = $entityObserver->count();
            ObserverLog::add($this->getObserver()->id, $count . ($count == 1 ? ' Task' : ' Tasks') . ' found for given filter.', __CLASS__ . ':' . __METHOD__);
        }
    
        // get model for the data container
        $model = \Model::getClassFromTable($this->getObserver()->dataContainer);
        
        if(null === ($entities = $model::findAll()))
        {
            if ($this->getObserver()->debug)
            {
                ObserverLog::add($this->getObserver()->id, 'No entities for given filter found.', __CLASS__ . ':' . __METHOD__);
            }
    
            return;
        }
    
        if ($this->getObserver()->debug)
        {
            $count = $entities->count();
            ObserverLog::add(
                $this->getObserver()->id,
                $count . ($count == 1 ? ' entity' : ' entities') . ' found for given source.',
                __CLASS__ . ':' . __METHOD__
            );
        }
    
        while ($entities->next())
        {
            $this->context = $entities->current();
    
            // TODO: add editable WHERE clause to entity observer
            if($this->context->{Entity::ENTITY_NOTIFYER_EXCLUDE_FROM_OBSERVER} || 0 == $this->context->tstamp || (isset($this->context->disable) && $this->context->disable) || (isset($this->context->published) && !$this->context->published))
            {
                continue;
            }
    
            if (!$this->waitForContext($this->context))
            {
                if ($this->getObserver()->debug)
                {
                    ObserverLog::add($this->getObserver()->id, 'Observers updated with task: "' . $entityObserver->title . '" [ID:' . $entityObserver->id .  '].', __CLASS__ . ':' . __METHOD__);
                }
    
                
                foreach ($this->observers as $obs)
                {
                    $obs->update($this);
                }
                
                continue;
            }
            
            if ($this->getObserver()->debug)
            {
                ObserverLog::add($this->getObserver()->id, 'Waiting time for task: "' . $entityObserver->title . '" [ID:' . $entityObserver->id .  '] not elapsed yet.', __CLASS__ . ':' . __METHOD__);
            }
        }
        
        return true;
    }
}