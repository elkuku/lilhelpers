<?php

//@BAD -- Space after opening parenthesis
$foo = ( 'Foo');

//@GOOD -- Space after opening parenthesis followed by ! is allowed
$foo = ( ! 'Foo');

//@BAD -- Space after opening parenthesis followed by ! is required
$foo = (! 'Foo');
$foo = ( !'Foo');

//@BAD -- Space before closing parenthesis
$foo = ('Foo' );

//@GOOD -- Space before closing parenthesis preceded by a EOL
$foo = ( ! 'Foo'
);
