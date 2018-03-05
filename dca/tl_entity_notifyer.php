<?php
/**
 * Created by PhpStorm.
 * User: mkunitzsch
 * Date: 01.03.18
 * Time: 11:03
 */

$GLOBALS['TL_DCA']['tl_entity_notifyer'] = [
    'config'   => [
        'dataContainer'     => 'Table',
        'enableVersioning'  => true,
        'onsubmit_callback' => [
            ['HeimrichHannot\Haste\Dca\General', 'setDateAdded'],
        ],
        'sql'               => [
            'keys' => [
                'id' => 'primary'
            ]
        ]
    ],
    'list'     => [
        'label'             => [
            'fields' => ['title'],
            'format' => '%s'
        ],
        'sorting'           => [
            'mode'         => 1,
            'fields'       => ['title'],
            'headerFields' => ['title'],
            'panelLayout'  => 'filter;search,limit'
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ],
        ],
        'operations'        => [
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_entity_notifyer']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif'
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_entity_notifyer']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif'
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_entity_notifyer']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                                . '\'))return false;Backend.getScrollOffset()"'
            ],
            'toggle' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_entity_notifyer']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => ['tl_entity_notifyer', 'toggleIcon']
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_entity_notifyer']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
            ],
        ]
    ],
    'palettes' => [
        'default'     => '{entity_observer_legend},title,observer,notification,published;',
    ],
    'fields'   => [
        'id'            => [
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],
        'tstamp'        => [
            'label' => &$GLOBALS['TL_LANG']['tl_entity_notifyer']['tstamp'],
            'sql'   => "int(10) unsigned NOT NULL default '0'"
        ],
        'dateAdded'     => [
            'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
            'sorting' => true,
            'flag'    => 6,
            'eval'    => ['rgxp' => 'datim', 'doNotCopy' => true],
            'sql'     => "int(10) unsigned NOT NULL default '0'"
        ],
        'title'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_entity_notifyer']['title'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'published'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_entity_notifyer']['published'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'clr w50', 'doNotCopy' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ],
        'notification' => [
            'label' => &$GLOBALS['TL_LANG']['tl_entity_notifyer']['notification'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'select',
            'options_callback' => ['HeimrichHannot\EntityNotifyer\Backend\EntityNotifyerHelper', 'getNotifications'],
            'eval'      => ['maxlength' => 255, 'mandatory' => true, 'tl_class' => 'clr w50'],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'observer'                               => [
            'label'      => &$GLOBALS['TL_LANG']['tl_entity_notifyer']['observer'],
            'exclude'    => true,
            'inputType'  => 'select',
            'foreignKey' => 'tl_observer.title',
            'eval'       => ['tl_class' => 'w50 clr', 'includeBlankOption' => true, 'mandatory' => true],
            'sql'        => "int(8) unsigned NOT NULL default '0'",

        ],
    ]
];


class tl_entity_notifyer extends \Contao\Backend
{
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        $objUser = \BackendUser::getInstance();
        
        if (strlen(Input::get('tid')))
        {
            $this->toggleVisibility(Input::get('tid'), (Input::get('state') === '1'));
            \Controller::redirect($this->getReferer());
        }
        
        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$objUser->isAdmin && !$objUser->hasAccess('tl_entity_notifyer::published', 'alexf'))
        {
            return '';
        }
        
        $href .= '&amp;tid=' . $row['id'] . '&amp;state=' . ($row['published'] ? '' : 1);
        
        if (!$row['published'])
        {
            $icon = 'invisible.gif';
        }
        
        return '<a href="' . $this->addToUrl($href) . '" title="' . specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label)
               . '</a> ';
    }
    
    public function toggleVisibility($intId, $blnVisible)
    {
        $objUser     = \BackendUser::getInstance();
        $objDatabase = \Database::getInstance();
        
        // Check permissions to publish
        if (!$objUser->isAdmin && !$objUser->hasAccess('tl_entity_notifyer::published', 'alexf'))
        {
            \Controller::log('Not enough permissions to publish/unpublish item ID "' . $intId . '"', 'tl_entity_notifyer toggleVisibility', TL_ERROR);
            \Controller::redirect('contao/main.php?act=error');
        }
        
        $objVersions = new Versions('tl_cleaner', $intId);
        $objVersions->initialize();
        
        // Trigger the save_callback
        if (is_array($GLOBALS['TL_DCA']['tl_entity_notifyer']['fields']['published']['save_callback']))
        {
            foreach ($GLOBALS['TL_DCA']['tl_entity_notifyer']['fields']['published']['save_callback'] as $callback)
            {
                $this->import($callback[0]);
                $blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, $this);
            }
        }
        
        // Update the database
        $objDatabase->prepare("UPDATE tl_entity_notifyer SET tstamp=" . time() . ", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")->execute(
            $intId
        );
        
        $objVersions->create();
        \Controller::log(
            'A new version of record "tl_entity_notifyer.id=' . $intId . '" has been created' . $this->getParentEntries('tl_entity_notifyer', $intId),
            'tl_entity_notifyer toggleVisibility()',
            TL_GENERAL
        );
    }
}