<?php

/*  Copyright 2014 Sutherland Boswell  (email : sutherland.boswell@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Require thumbnail provider class
require_once( RTI_VIDEO_THUMBNAIL_PATH . '/providers/class-video-thumbnails-provider.php' );

class Metacafe_Thumbnails extends Video_Thumbnails_Provider {

	// Human-readable name of the video provider
	public $service_name = 'Metacafe';
	const service_name = 'Metacafe';
	// Slug for the video provider
	public $service_slug = 'metacafe';
	const service_slug = 'metacafe';

	public static function register_provider( $providers ) {
		$providers[self::service_slug] = new self;
		return $providers;
	}

	// Regex strings
	public $regexes = array(
		'#http://www\.metacafe\.com/fplayer/([A-Za-z0-9\-_]+)/#' // Metacafe embed
	);

	// Thumbnail URL
	public function get_thumbnail_url( $id ) {
		$request = "http://www.metacafe.com/api/item/$id/";
		$response = wp_remote_get( $request );
		if( is_wp_error( $response ) ) {
			$result = $this->construct_info_retrieval_error( $request, $response );
		} else {
			$xml = new SimpleXMLElement( $response['body'] );
			$result = $xml->xpath( "/rss/channel/item/media:thumbnail/@url" );
			$result = (string) $result[0]['url'];
			$result = $this->drop_url_parameters( $result );
		}
		return $result;
	}

	// Test cases
	public static function get_test_cases() {
		return array(
			array(
				'markup'        => '<embed flashVars="playerVars=autoPlay=no" src="http://www.metacafe.com/fplayer/8456223/men_in_black_3_trailer_2.swf" width="440" height="248" wmode="transparent" allowFullScreen="true" allowScriptAccess="always" name="Metacafe_8456223" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>',
				'expected'      => 'http://s4.mcstatic.com/thumb/8456223/22479418/4/catalog_item5/0/1/men_in_black_3_trailer_2.jpg',
				'expected_hash' => '977187bfb00df55b39724d7de284f617',
				'name'          => __( 'Flash Embed', 'video-thumbnails' )
			),
		);
	}

}

?>
