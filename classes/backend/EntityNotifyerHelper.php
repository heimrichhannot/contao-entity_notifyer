<?php
/**
 * Created by PhpStorm.
 * User: mkunitzsch
 * Date: 01.03.18
 * Time: 11:18
 */

namespace HeimrichHannot\EntityNotifyer\Backend;


use Contao\Backend;
use Contao\Environment;
use Contao\Image;
use HeimrichHannot\EntityNotifyer\Entity;
use NotificationCenter\Model\Notification;
use Contao\Message;
use Contao\Controller;

class EntityNotifyerHelper extends Backend
{
    /**
     * return all fields for set dataContainer
     *
     * @param \DataContainer $dc
     *
     * @return array
     */
    public static function getFields(\DataContainer $dc)
    {
        $table = $dc->activeRecord->dataContainer;
        
        
        if (!$table)
        {
            return [];
        }
        
        \Controller::loadDataContainer($table);
    
        $fields = [];
        foreach ($GLOBALS['TL_DCA'][$table]['fields'] as $name => $data)
        {
            if (in_array($name, ['id']))
            {
                continue;
            }
        
            $fields[] = $name;
        }
        
        return $fields;
    }
    
    /**
     * return all notifications
     *
     * @param \DataContainer $dc
     *
     * @return array
     */
    public static function getNotifications(\DataContainer $dc)
    {
        $options = [];
    
        if(null === ($notifications = Notification::findAll()))
        {
            return $options;
        }
        
        return $notifications->fetchEach('title');
    
    }

    /**
    * Return the "excludeFromEntityObserver" button
    *
    * @param array $row
    * @param string $href
    * @param string $label
    * @param string $title
    * @param string $icon
    * @param string $attributes
    *
    * @return string
    */
    public function excludeFromEntityObserver($row, $href, $label, $title, $icon, $attributes)
    {
        $this->import('BackendUser','User');
        
        if($row['excludeFromEntityObserver'])
        {
            $href  = 'key='.Entity::ENTITY_NOTIFYER_INCLUDE;
            $title = sprintf($GLOBALS['TL_LANG']['OBSERVER'][Entity::ENTITY_NOTIFYER_INCLUDE][1], $row['id']);
            $label = $GLOBALS['TL_LANG']['OBSERVER'][Entity::ENTITY_NOTIFYER_INCLUDE][0];
        
            return '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ';
        }
        
        $icon = 'system/modules/entity_observer/assets/img/icon.png';
    
        return '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ';
    }
    
    /**
     * set entity to be excluded in entity observer notification
     *
     * @param \DataContainer $dc
     */
    public function doExcludeFromEntityObserver(\DataContainer $dc)
    {
        $redirect = str_replace('&key='.Entity::ENTITY_NOTIFYER_EXCLUDE, '', Environment::get('request'));
    
        $model = \Model::getClassFromTable($dc->table);
    
        if(null === ($entity = $model::findByPk($dc->id)))
        {
            Message::addError(sprintf($GLOBALS['TL_LANG']['MSC']['entityNotFound'], $dc->id));
            Controller::redirect($redirect);
        }
        
        $entityExcluded = $entity->excludeFromEntityObserver;
        
        if($entityExcluded)
        {
            Message::addError(sprintf($GLOBALS['TL_LANG']['MSC']['entityAlreadyExcluded'], $dc->id));
            Controller::redirect($redirect);
        }
    
        $entity->excludeFromEntityObserver = 1;
        $entity->save();
    
        Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['MSC']['entityExcludeSuccess'], $dc->table ?: $dc->id));
        Controller::redirect($redirect);
    }


    /**
     * set entity to be included in entity observer notification
     *
     * @param \DataContainer $dc
     */
    public function includeEntityToEntityObserver(\DataContainer $dc)
    {
        $redirect = str_replace('&key='.Entity::ENTITY_NOTIFYER_INCLUDE, '', Environment::get('request'));
    
        $model = \Model::getClassFromTable($dc->table);
    
        if(null === ($entity = $model::findByPk($dc->id)))
        {
            Message::addError(sprintf($GLOBALS['TL_LANG']['MSC']['entityNotFound'], $dc->id));
            Controller::redirect($redirect);
        }
    
        $entityExcluded = $entity->excludeFromEntityObserver;
    
        if(!$entityExcluded)
        {
            Message::addError(sprintf($GLOBALS['TL_LANG']['MSC']['entityAlreadyIncluded'], $dc->id));
            Controller::redirect($redirect);
        }
    
        $entity->excludeFromEntityObserver = '';
        $entity->save();
    
        Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['MSC']['entityIncludeSuccess'], $dc->table ?: $dc->id));
        Controller::redirect($redirect);
    }
    
    /**
     * add excludeFromEntityObserver field to dca
     *
     * @param string $table
     */
    public static function addExcludeField($table)
    {
        \Controller::loadDataContainer($table);
        \System::loadLanguageFile($table);
        
        $dca = &$GLOBALS['TL_DCA'][$table];
        
        $dca['fields'][Entity::ENTITY_NOTIFYER_EXCLUDE_FROM_OBSERVER] = [
            'label'     => &$GLOBALS['TL_LANG'][$table][Entity::ENTITY_NOTIFYER_EXCLUDE_FROM_OBSERVER],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'sql'       => "varchar(1) NOT NULL default ''"
        ];
    }
}