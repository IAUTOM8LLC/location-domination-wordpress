<?PHP

/**
 * Spintax - A helper class to process Spintax strings.
 *
 * @name Spintax
 * @author Jason Davis - https://www.codedevelopr.com/
 * Tutorial: https://www.codedevelopr.com/articles/php-spintax-class/
 */
class Spintax {

    public function setSeed( $seed ) {
        srand( $seed );
    }

    public function process( $text ) {
        return preg_replace_callback(
            '/\{(((?>[^\{\}]+)|(?R))*?)\}/x',
            array( $this, 'replace' ),
            $text
        );
    }

    public function replace( $text ) {
        $text  = $this->process( $text[ 1 ] );
        $parts = explode( '|', $text );

        return $parts[ array_rand( $parts ) ];
    }
}


/* EXAMPLE USAGE */
$spintax = new Spintax();
$string  = "{Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {Smith|Williams|Davis}!" . "\n";
echo $spintax->process( $string );

/* NESTED SPINNING EXAMPLE */
echo $spintax->process( "{Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}" ) . "\n";

/* SET SEED EXAMPLE */
// Sets the seed for the random function. As result, the spin will always output the same text
// no matter how many times you process it. Text Id 100 will always be Hello
$idText = 101;
$spintax->setSeed( $idText );
$textId100 = $spintax->process( "{Hello|Hi!} {man|guy}" ) . "\n";
echo $textId100;