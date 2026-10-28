<?php
/**
 * Decode a CDP Page.captureScreenshot JSON response into screenshot.png.
 *
 * @package Fashion_Brand_Theme
 */

$json_path = $argv[1] ?? '';
$out_path  = $argv[2] ?? dirname( __DIR__ ) . '/screenshot.png';

if ( ! $json_path || ! file_exists( $json_path ) ) {
	fwrite( STDERR, "Usage: php decode-screenshot.php <cdp-json> [out.png]\n" );
	exit( 1 );
}

$raw = file_get_contents( $json_path );
$data = json_decode( $raw, true );

$b64 = null;
if ( is_array( $data ) ) {
	if ( isset( $data['data'] ) && is_string( $data['data'] ) ) {
		$b64 = $data['data'];
	} elseif ( isset( $data['result']['data'] ) && is_string( $data['result']['data'] ) ) {
		$b64 = $data['result']['data'];
	}
}

if ( ! $b64 ) {
	fwrite( STDERR, "No screenshot data found in JSON.\n" );
	exit( 1 );
}

$bin = base64_decode( $b64, true );
if ( false === $bin ) {
	fwrite( STDERR, "Failed to base64-decode screenshot data.\n" );
	exit( 1 );
}

if ( false === file_put_contents( $out_path, $bin ) ) {
	fwrite( STDERR, "Failed to write {$out_path}\n" );
	exit( 1 );
}

$info = @getimagesize( $out_path );
echo $out_path . "\n";
echo 'bytes=' . strlen( $bin ) . "\n";
if ( is_array( $info ) ) {
	echo 'size=' . $info[0] . 'x' . $info[1] . "\n";
}
