<?php

//@GOOD -- No Space before or after "."
$foo = 'Bar'.'Baz';

//@BAD -- Space before "."
$foo = 'Bar' .'Baz';

//@BAD -- Space after "."
$foo = 'Bar'. 'Baz';
