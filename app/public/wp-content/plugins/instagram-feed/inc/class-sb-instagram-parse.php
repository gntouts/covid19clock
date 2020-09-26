<?php
/**
 * Class SB_Instagram_Parse
 *
 * The structure of the data coming from the Instagram API is different
 * for the old API vs the new graph API. This class is used to parse
 * whatever structure the data has as well as use this to generate
 * parts of the html used for image sources.
 *
 * @since 2.0/5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class SB_Instagram_Parse
{
	/**
	 * @param $post array
	 *
	 * @return mixed
	 *
	 * @since 2.0/5.0
	 */
	public static function get_post_id( $post ) {
		return $post['id'];
	}

	/**
	 * @param $post array
	 *
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	public static function get_account_type( $post ) {
		if (isset( $post['media_type'] ) ) {
			return 'business';
		} else {
			return 'personal';
		}
	}

	/**
	 * @param $post array
	 *
	 * @return false|int
	 *
	 * @since 2.0/5.0
	 */
	public static function get_timestamp( $post ) {
		$timestamp = 0;
		if ( isset( $post['created_time'] ) ) {
			$timestamp = $post['created_time'];
		} else if ( isset( $post['timestamp'] ) ) {
			// some date formatting functions have trouble with the "T", "+", and extra zeroes added by Instagram
			$remove_plus = trim( str_replace( array('T', '+', ' 0000' ), ' ', $post['timestamp'] ) );
			$timestamp = strtotime( $remove_plus );
		}

		return $timestamp;
	}

	/**
	 * @param $post array
	 *
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	public static function get_media_type( $post ) {
		if ( isset( $post['type'] ) ) {
			return $post['type'];
		}

		return strtolower( str_replace( '_ALBUM','', $post['media_type'] ) );
	}

	/**
	 * @param $post array
	 *
	 * @return mixed
	 *
	 * @since 2.0/5.0
	 */
	public static function get_permalink( $post ) {
		if ( isset( $post['permalink'] ) ) {
			return $post['permalink'];
		}

		return $post['link'];
	}

	/**
	 * @param array $post
	 * @param string $resolution
	 *
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	public static function get_media_url( $post, $resolution = 'lightbox' ) {
		$account_type = isset( $post['images'] ) ? 'personal' : 'business';

		if ( $account_type === 'personal' ) {
			return $post['images']['standard_resolution']['url'];
		} else {
			if ($post['media_type'] === 'CAROUSEL_ALBUM' || $post['media_type'] === 'VIDEO') {
				if ( isset( $post['thumbnail_url'] ) ) {
					return $post['thumbnail_url'];
				} elseif ( $post['media_type'] === 'CAROUSEL_ALBUM' && isset( $post['media_url'] ) ) {
					return $post['media_url'];
				} else {
					$permalink = SB_Instagram_Parse::fix_permalink( SB_Instagram_Parse::get_permalink( $post ) );

					return $permalink . 'media/?size=l';
				}
			} else {
				if ( isset( $post['media_url'] ) ) {
					return $post['media_url'];
				}
				$permalink = SB_Instagram_Parse::fix_permalink( SB_Instagram_Parse::get_permalink( $post ) );

				return $permalink . 'media/?size=l';
			}
		}

	}

	/**
	 * Uses the existing data for the indvidual instagram post to
	 * set the best image sources for each resolution size. Due to
	 * random bugs or just how the API works, different post types
	 * need special treatment.
	 *
	 * @param array $post
	 * @param array $resized_images
	 *
	 * @return array
	 *
	 * @since 2.0/5.0
	 * @since 2.1.3/5.2.3 added 'd' element as a default backup from the API
	 */
	public static function get_media_src_set( $post, $resized_images = array() ) {
		$media_urls = array(
			'd' => SB_Instagram_Parse::get_media_url( $post ),
			'150' => '',
			'320' => '',
			'640' => ''
		);
		$account_type = isset( $post['images'] ) ? 'personal' : 'business';


		if ( $account_type === 'personal' ) {
			$media_urls['150'] = $post['images']['thumbnail']['url'];
			$media_urls['320'] = $post['images']['low_resolution']['url'];
			$media_urls['640'] = $post['images']['standard_resolution']['url'];
		} else {
			$post_id = SB_Instagram_Parse::get_post_id( $post );

			$permalink = SB_Instagram_Parse::fix_permalink( SB_Instagram_Parse::get_permalink( $post ) );

			if ( ($post['media_type'] === 'CAROUSEL_ALBUM' || $post['media_type'] === 'VIDEO') && ($media_urls['640'] === '' || $media_urls['640'] === 'video' || $media_urls['640'] === 'pending')) {
				$media_urls['640'] = $permalink . 'media/?size=l';
			} else {
				$media_urls['640'] = isset( $post['media_url'] ) ? $post['media_url'] : $permalink . 'media/?size=l';
			}
			$media_urls['150'] = $permalink . 'media/?size=t';
			$media_urls['320'] = $permalink . 'media/?size=m';

			// use resized images if exists
			if ( isset( $resized_images[ $post_id ]['id'] )
			     && $resized_images[ $post_id ]['id'] !== 'pending'
			     && $resized_images[ $post_id ]['id'] !== 'video'
			     && $resized_images[ $post_id ]['id'] !== 'error' ) {
				if ( isset( $resized_images[ $post_id ]['sizes']['full'] ) ) {
					$media_urls['640'] = sbi_get_resized_uploads_url() . $resized_images[ $post_id ]['id'] . 'full.jpg';
				}
				if ( isset( $resized_images[ $post_id ]['sizes']['low'] ) ) {
					$media_urls['320'] = sbi_get_resized_uploads_url() . $resized_images[ $post_id ]['id'] . 'low.jpg';
				}
			}

		}

		return $media_urls;
	}

	/**
	 * A default can be set in the case that the user doesn't use captions
	 * for posts as this is also used as the alt text for the image.
	 *
	 * @param $post
	 * @param string $default
	 *
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	public static function get_caption( $post, $default = '' ) {
		$caption = $default;
		if ( ! empty( $post['caption'] ) && ! is_array( $post['caption'] ) ) {
			$caption = $post['caption'];
		} elseif ( ! empty( $post['caption']['text'] ) ) {
			$caption = $post['caption']['text'];
		}

		return $caption;
	}

	/**
	 * @param array $header_data
	 *
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	public static function get_username( $header_data ) {
		if ( isset( $header_data['username'] ) ) {
			return $header_data['username'];
		} elseif ( isset( $header_data['user'] ) ) {
			return $header_data['user']['username'];
		} elseif ( isset( $header_data['data'] ) ) {
			return $header_data['data']['username'];
		}
		return '';
	}

	/**
	 * @param array $header_data
	 * @param array $settings
	 *
	 * @return string
	 *
	 * @since 2.0/5.0
	 * @since 2.2/5.3 added support for a custom avatar in settings
	 */
	public static function get_avatar( $header_data, $settings = array( 'favor_local' => false ) ) {
		if ( ! empty( $settings['customavatar'] ) ) {
			return $settings['customavatar'];
		} elseif ( ! empty( $header_data['local_avatar'] ) ) {
			return $header_data['local_avatar'];
		} else {
			if ( isset( $header_data['profile_picture'] ) ) {
				return $header_data['profile_picture'];
			} elseif ( isset( $header_data['profile_picture_url'] ) ) {
				return $header_data['profile_picture_url'];
			} elseif ( isset( $header_data['user'] ) ) {
				return $header_data['user']['profile_picture'];
			} elseif ( isset( $header_data['data'] ) ) {
				return $header_data['data']['profile_picture'];
			}
		}

		return '';
	}

	/**
	 * The full name attached to the user account
	 *
	 * @param array $header_data
	 *
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	public static function get_name( $header_data ) {
		if ( isset( $header_data['name'] ) ) {
			return $header_data['name'];
		} elseif ( isset( $header_data['data']['full_name'] ) ) {
			return $header_data['data']['full_name'];
		}
		return SB_Instagram_Parse::get_username( $header_data );
	}

	/**
	 * Account bio/description used in header
	 *
	 * @param $header_data
	 *
	 * @return string
	 *
	 * @since 2.0.1/5.0
	 * @since 2.2/5.3 added support for a custom bio in settings
	 */
	public static function get_bio( $header_data, $settings = array() ) {
		if ( ! empty( $settings['custombio'] ) ) {
			return $settings['custombio'];
		} elseif ( isset( $header_data['data']['bio'] ) ) {
			return $header_data['data']['bio'];
		} elseif ( isset( $header_data['bio'] ) ){
			return $header_data['bio'];
		} elseif ( isset( $header_data['biography'] ) ){
			return $header_data['biography'];
		}
		return '';
	}

	/**
	 * There seems to be occasional bugs with the Instagram API
	 * and permalinks. This corrects it.
	 *
	 * @param string $permalink
	 *
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	protected static function fix_permalink( $permalink ) {
		if ( substr_count( $permalink, '/' ) > 5 ) {
			$permalink_array = explode( '/', $permalink );
			$perm_id = $permalink_array[ count( $permalink_array ) - 2 ];
			$permalink = 'https://www.instagram.com/p/' . $perm_id . '/';
		}

		return $permalink;
	}
}