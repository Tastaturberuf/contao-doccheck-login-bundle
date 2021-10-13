<?php


use Tastaturberuf\ContaoDoccheckLoginBundle\Controller\DoccheckLoginController;


(function(string $table, \Contao\DcaLoader $loader)
{
    $loader::getLanguages();

    if (!isset($GLOBALS['TL_DCA'][$table]))
    {
        $GLOBALS['TL_DCA'][$table] = [];
    }

    $GLOBALS['TL_DCA'][$table] = \array_replace_recursive($GLOBALS['TL_DCA'][$table], [
        'palettes' =>
        [
            DoccheckLoginController::NAME =>
            '
                {title_legend},name,headline,type;
                {doccheck_legend},doccheck_login_id,doccheck_user,doccheck_jump_to;
                {template_legend},customTpl,doccheck_style,doccheck_language;
                {protected_legend:hide},protected;
                {expert_legend:hide},guests,cssID,doccheck_frame_option
            '
        ],
        'fields' =>
        [
            'doccheck_login_id' =>
            [
                'inputType' => 'text',
                'eval' =>
                [
                    'mandatory' => true,
                    'tl_class' => 'w50'
                ],
                'sql' => "varchar(16) NOT NULL default ''"
            ],
            'doccheck_user' =>
            [
                'inputType' => 'select',
                'foreignKey' => 'tl_member.username',
                'eval'       =>
                [
                    'mandatory'          => true,
                    'includeBlankOption' => true,
                    'tl_class'           => 'w50'
                ],
                'sql' => "int(10) unsigned NOT NULL default 0"
            ],
            'doccheck_style' =>
            [
                'inputType' => 'select',
                'options'   =>
                [
                    'login_s',
                    'login_m',
                    'login_l',
                    'login_xl'
                ],
                'eval' =>
                [
                    'tl_class' => 'clr w50'
                ],
                'sql' => "varchar(8) NOT NULL default 'login_s'"
            ],
            'doccheck_language' =>
            [
                'inputType' => 'select',
                'options'   =>
                [
                    'de'   => $GLOBALS['TL_LANG']['LNG']['de'],
                    'com'  => $GLOBALS['TL_LANG']['LNG']['en'],
                    'befr' => $GLOBALS['TL_LANG']['LNG']['fr'],
                    'it'   => $GLOBALS['TL_LANG']['LNG']['it'],
                    'benl' => $GLOBALS['TL_LANG']['LNG']['nl'],
                    'es'   => $GLOBALS['TL_LANG']['LNG']['es']
                ],
                'eval' =>
                [
                    'tl_class' => 'w50'
                ],
                'sql' => "varchar(4) NOT NULL default 'de'"
            ],
            'doccheck_frame_option' =>
            [
                'inputType' => 'select',
                'options'    =>
                [
                    '_top',
                    '_parent',
                    '_self',
                    '_blank'
                ],
                'eval' =>
                [
                    'tl_class' => 'clr w50'
                ],
                'sql' => "varchar(7) NOT NULL default '_top'"
            ],
            'doccheck_jump_to' =>
            [
                'label'      => &$GLOBALS['TL_LANG'][$table]['reg_jumpTo'],
                'exclude'    => true,
                'inputType'  => 'pageTree',
                'foreignKey' => 'tl_page.title',
                'eval'       =>
                [
                    'mandatory' => false,
                    'fieldType' => 'radio',
                    'tl_class'  => 'clr w50'
                ],
                'relation' =>
                [
                    'type' => 'hasOne',
                    'load' =>'lazy'
                ],
                'sql' => "int(10) unsigned NOT NULL default 0",
            ]
        ]
    ]);

})($this->strTable, $this);
