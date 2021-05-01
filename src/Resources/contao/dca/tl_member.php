<?php

/**
 * Doccheck Login Bundle for Contao Open Source CMS
 * @copyright (c) 2021 Tastaturberuf
 * @author    Daniel Jahnsmüller <https://tastaturberuf.de>
 */

declare(strict_types=1);


$GLOBALS['TL_DCA']['tl_member']['fields']['doccheck_user'] =
[
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' =>
    [
        'unique'   => true,
        'tl_class' => 'm12 w50'
    ],
    'sql' => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_member']['fields']['doccheck_redirect'] =
[
    'exclude'                 => true,
    'inputType'               => 'pageTree',
    'foreignKey'              => 'tl_page.title',
    'eval'                    => ['fieldType'=>'radio','tl_class'=>'w50 widget cbx'],
    'sql'                     => "int(10) unsigned NOT NULL default 0",
    'relation'                => array('type'=>'hasOne', 'load'=>'lazy')
];

\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('doccheck_legend', 'login_legend', 'append')
    ->addField(['doccheck_user', 'doccheck_redirect'], 'doccheck_legend')
    ->applyToPalette('default', 'tl_member')
;
