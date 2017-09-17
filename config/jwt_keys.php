<?php


/* Securing Plugin From Direct Access through the URL Path */
if ( ! defined( 'ABSPATH' ) ) exit;

/* Public & Private Keys For JWT */

/*
	Use this website to generate New Keys:-
	http://travistidwell.com/jsencrypt/demo/
	( Use Key Size: 1024 bit and Check "Async" checkbox )
*/





$dd_mobile_app_jwt_keys	= json_decode( get_option('dd_mobile_app_jwt_keys') );



define( 'JWT_PRIVATE_KEY' , 
	$dd_mobile_app_jwt_keys && $dd_mobile_app_jwt_keys->jwt_private_key
	?
	$dd_mobile_app_jwt_keys->jwt_private_key
	:
'-----BEGIN RSA PRIVATE KEY-----
MIICWwIBAAKBgH92J9i9HI7snQhS35I+d36f5k/08Mwrc1sQu7AEaQaFSCTUrDa+
Z0edkT8vGkwe4/3o1k/Gdr4bzWhl7uuNTq9ow6LtX9Xk67NusKx1AaGXbJrjDY8P
TmA2YlBiFXLE5PTcRb6oFQez7P9qHQeMt87KnjJ83ii+3lwSloqACoNxAgMBAAEC
gYBzhpxHDeC8iikqSjLpUkTsN/F/tvopk5hSibATsWufxV3GRBxy4dCqUF49bmvf
YoAyur7EP5WQaZKbdQg/5yvQ+SZdxcVzFx9VyJPSUk2cj44sqtk3TnFGFxZkb38N
VnxW6syr+CZDhPlIxGurHYYGYsZu7k5smtfXk9lxSxW3IQJBALXVGD9H2XA9NUwG
ckiWLnoOjbKeb2zneRIqy2zUseA0IHavKs3jiliVNzdIVZrh7jc30OWrXqWU6PH8
yoRBUU8CQQCzc6qWxclfvjGPVUK7b2tnYU8JuWokn5C83dxwJwMRQ4LBoRlDCBmh
1Hbxi25cyBqHsOphHRcVO9rbKEKi1i8/AkBa79/Yhg5w26u0AeVV+AX2dSDYm/QN
+xXJyC1EmHr9LhSuRpyZq5qdAsNCmqJ1e1ivtPJ8amBDX13A6DRAQIpxAkEAncP1
2OIAPYVHqvzJU2IMafH5+9GfyJzkMbdXwt35a2cQAM1yGSV463aQL6FmoHloIZ1f
IYvirE9YBM7rbp+hHwJAAoi8WFLaiz5dbraRZZtBek4LU3Tajdr2fckJ2V3Nb3ip
5O4IjrZY7zrZKzUlU9r1XoUYzRefPsBDjK7ChRhBsw==
-----END RSA PRIVATE KEY-----'
);





define( 'JWT_PUBLIC_KEY' , 
	$dd_mobile_app_jwt_keys && $dd_mobile_app_jwt_keys->jwt_public_key
	?
	$dd_mobile_app_jwt_keys->jwt_public_key
	:
'-----BEGIN PUBLIC KEY-----
MIGeMA0GCSqGSIb3DQEBAQUAA4GMADCBiAKBgH92J9i9HI7snQhS35I+d36f5k/0
8Mwrc1sQu7AEaQaFSCTUrDa+Z0edkT8vGkwe4/3o1k/Gdr4bzWhl7uuNTq9ow6Lt
X9Xk67NusKx1AaGXbJrjDY8PTmA2YlBiFXLE5PTcRb6oFQez7P9qHQeMt87KnjJ8
3ii+3lwSloqACoNxAgMBAAE=
-----END PUBLIC KEY-----'
);











?>