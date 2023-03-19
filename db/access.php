<?php
$capabilities = array(

    'paygw/docente:reportcartadocente' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
        ),
        'clonepermissionsfrom' => 'moodle/site:config',
    )
);
