<?php return array(
    'root' => array(
        'pretty_version' => '1.0.0+no-version-set',
        'version' => '1.0.0.0',
        'type' => 'project',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'reference' => NULL,
        'name' => 'jp/Backend',
        'dev' => true,
    ),
    'versions' => array(
        'edesk/edesk-core' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'type' => 'library',
            'install_path' => __DIR__ . '/../edesk/edesk-core',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'reference' => '0f714140214906ead650695341f679f92367059f',
            'dev_requirement' => false,
        ),
        'jp/Backend' => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version' => '1.0.0.0',
            'type' => 'project',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'reference' => NULL,
            'dev_requirement' => false,
        ),
    ),
);
