<?php
/**
 * Created by PhpStorm.
 * User: mkunitzsch
 * Date: 01.03.18
 * Time: 14:40
 */

$dca = &$GLOBALS['TL_DCA']['tl_observer'];


$dca['palettes'][\HeimrichHannot\EntityNotifyer\Entity::OBSERVER_SUBJECT_ENTITY_NOTIFICATION] = '{general_legend},subject,title;
    {cronjob_legend},cronInterval,useCronExpression,priority,invoked,invokedState,addContextAge;
    {observer_legend},observer;
    {expert_legend},debug,addObserverStates;
    {publish_legend},published;';

$dca['subpalettes']['addContextAge'] = 'dataContainer,contextAgeAttribute,contextAge';

$dca['fields']['dataContainer'] = [
    'inputType'        => 'select',
    'label'            => &$GLOBALS['TL_LANG']['tl_observer']['dataContainer'],
    'options_callback' => ['HeimrichHannot\Haste\Dca\General', 'getDataContainers'],
    'eval'             => [
        'chosen'             => true,
        'includeBlankOption' => true,
        'tl_class'           => 'w50 clr',
        'submitOnChange'     => true,
        'mandatory'          => true
    ],
    'exclude'          => true,
    'sql'              => "varchar(255) NOT NULL default ''",
];
