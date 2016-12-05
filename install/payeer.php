<?php
/**
 * Payeer payment module
 * 
 * @package  yupe.modules.payeer
 * @author   24-shops <24-shops@mail.ru>
 * @license  BSD http://ru.wikipedia.org/wiki/%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F_BSD
 * @link     https://github.com/24-shops/yupe-payeer
 **/

return [
  'module'=>[
    'class'=>'application.modules.payeer.PayeerModule',
  ],
  'import'=>[
    'application.modules.payeer.helpers.*',
    'application.modules.payeer.PayeerModule',
    //'application.modules.payeer.listeners.PayeerTemplateListener',
  ],
  'component'=>[
    'paymentManager'=>[
      'paymentSystems'=>[
        'payeer'=>[
          'class'=>'application.modules.payeer.components.payments.PayeerPaymentSystem',
        ]
      ],
    ],
    'payeerBackend'=>[
      'class'=>'application.modules.payeer.components.PayeerBackend',
    ],
    /*'callbackManager'=>[
      'class'=>'application.modules.callback.components.CallbackManager',
    ],
    'eventManager'=>[
      'class'=>'yupe\components\EventManager',
      'events'=>[
        'template.head.end'=>[
          ['CallbackTemplateListener','js'],
        ],
      ],
    ],*/
  ],
  'rules'=>[
    '/payeer'=>'/payment/payeer/',
    '/payeer'=>'/payment/payeer/callback',
  ],
];

?>