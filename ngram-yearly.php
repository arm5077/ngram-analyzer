<?PHP

$ngram      = 1;
$ngramArray = Array();
$text       = "";
$start      = 0;

$content = file_get_contents( "data.json" );
$json    = json_decode( $content );

$handle = fopen( "results.csv", "w+" );

foreach ( $json as $speech ) {
				//Eliminate phrases in [ ] and ( )
				$formatted = preg_replace( Array(
								 "#\(.*?\)#s",
								"#\[.*?\]#s" 
				), "", $speech->Text );
				//Eliminate grammatical marks except for ' and - 
				$formatted = preg_replace( "/(?!['-])\p{P}/u", "", $formatted );
				$formatted = preg_replace( "/\s{2,}/", " ", $formatted );
				$formatted = strtolower( $formatted );
				
				$textArray = explode( " ", $formatted );
				
				for ( $i = 0; $i < count( $textArray ) - $ngram; $i++ ) {
								$chunk = "";
								
								for ( $j = 0; $j < $ngram; $j++ ) {
												$chunk .= $textArray[ $i + $j ] . " ";
								}
								
								$chunk = trim( $chunk );
								if ( $ngramArray[ $chunk ][ $speech->Season ] == "" )
												$ngramArray[ $chunk ][ $speech->Season ] = 1;
								else
												$ngramArray[ $chunk ][ $speech->Season ]++;
				}
}

arsort( $ngramArray );

foreach ( $ngramArray as $key => $count ) {
				
				fseek( $handle, 0, SEEK_END );
				fwrite( $handle, $key . "," . $count[ 2010 ] . "," . $count[ 2011 ] . "," . $count[ 2012 ] . "," . $count[ 2013 ] . "\n" );
				
}
?>
