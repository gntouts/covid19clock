<?php
/**
 * Class SB_Instagram_API_Connect
 *
 * Connect to the Instagram API and return the results. It's possible
 * to build the url from a connected account (includes access token,
 * account id, account type), endpoint and parameters (hashtag, etc..)
 * as well as a full url such as from the pagination data from some Instagram API requests.
 *
 * Errors from either the Instagram API or from the HTTP request are detected
 * and can be handled.
 *
 * Primarily used in the SB_Instagram_Feed class to collect posts and data for
 * the header. Can also be used for comments in the Pro version
 *
 * @since 2.0/5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class SB_Instagram_API_Connect
{
	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var object
	 */
	protected $response;

	/**
	 * SB_Instagram_API_Connect constructor.
	 *
	 * @param mixed|array|string $connected_account_or_url either the connected account
	 *  data for this request or the complete url for the request
	 * @param string $endpoint (optional) is optional only if the complete url is provided
	 *  otherwise is they key for the endpoint needed for the request (ex. "header")
	 * @param array $params (optional) used with the connected account and endpoint to add
	 *  additional query parameters to the url if needed
	 *
	 * @since 2.0/5.0
	 */
	public function __construct( $connected_account_or_url, $endpoint = '', $params = array() ) {
		if ( is_array( $connected_account_or_url ) && isset( $connected_account_or_url['access_token'] ) ) {
			$this->set_url( $connected_account_or_url, $endpoint, $params );
		} elseif ( strpos( $connected_account_or_url, 'https' ) !== false ) {
			$this->url = $connected_account_or_url;
		} else {
			$this->url = '';
		}
	}

	/**
	 * Returns the response from Instagram
	 *
	 * @return object
	 *
	 * @since 2.0/5.0
	 */
	public function get_data() {
		if (!empty($this->response['data'])) {
			return $this->response['data'];
		} else {
			return $this->response;
		}
	}

	/**
	 * Returns the error response and the url that was trying to be connected to
	 * or false if no error
	 *
	 * @return array|bool
	 *
	 * @since 2.0/5.0
	 */
	public function get_wp_error() {
		if ( $this->is_wp_error() ) {
			return array( 'response' => $this->response, 'url' => $this->url );
		} else {
			return false;
		}
	}

	/**
	 * Certain endpoints don't include the "next" URL so
	 * this method allows using the "cursors->after" data instead
	 *
	 * @param $type
	 *
	 * @return bool
	 *
	 * @since 2.2.2/5.3.3
	 */
	public function type_allows_after_paging( $type ) {
		return false;
	}

	/**
	 * Returns the full url for the next page of the API request
	 *
	 * @param $type
	 *
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	public function get_next_page( $type = '' ) {
		if ( ! empty( $this->response['pagination']['next_url'] ) ) {
			return $this->response['pagination']['next_url'];
		} elseif ( ! empty( $this->response['paging']['next'] ) ) {
			return $this->response['paging']['next'];
		} else {
			if ( $this->type_allows_after_paging( $type ) ) {
				if ( isset( $this->response['paging']['cursors']['after'] ) ) {
					return $this->response['paging']['cursors']['after'];
				}
			}
			return '';
		}
	}

	/**
	 * If url needs to be generated from the connected account, endpoint,
	 * and params, this function is used to do so.
	 *
	 * @param $url
	 */
	public function set_url_from_args( $url ) {
		$this->url = $url;
	}

	/**
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * If the server is unable to connect to the url, returns true
	 *
	 * @return bool
	 *
	 * @since 2.0/5.0
	 */
	public function is_wp_error() {
		return is_wp_error( $this->response );
	}

	/**
	 * If the server can connect but Instagram returns an error, returns true
	 *
	 * @return bool
	 *
	 * @since 2.0/5.0
	 */
	public function is_instagram_error() {
		return (isset( $this->response['meta']['error_type'] ) || isset( $this->response['error']['message'] ));
	}

	/**
	 * Connect to the Instagram API and record the response
	 *
	 * @since 2.0/5.0
	 */
	public function connect() {
		$args = array(
			'timeout' => 60,
			'sslverify' => false
		);
		$response = wp_remote_get( $this->url, $args );

		if ( ! is_wp_error( $response ) ) {
			// certain ways of representing the html for double quotes causes errors so replaced here.
			$response = json_decode( str_replace( '%22', '&rdquo;', $response['body'] ), true );
		}

		$this->response = $response;
	}

	/**
	 * Determines how and where to record an error from Instagram's API response
	 *
	 * @param array $response response from the API request
	 * @param array $error_connected_account the connected account that is associated
	 *  with the error
	 * @param string $request_type key used to determine the endpoint (ex. "header")
	 *
	 * @since 2.0/5.0
	 */
	public static function handle_instagram_error( $response, $error_connected_account, $request_type ) {
		global $sb_instagram_posts_manager;
		delete_option( 'sbi_dismiss_critical_notice' );

		$sb_instagram_posts_manager->update_error_page( get_the_ID() );

		$error_time = 300;
		if ( isset( $response['error']['message'] ) ) {
			if ( (int)$response['error']['code'] === 100 ) {
				$options = get_option( 'sb_instagram_settings', array() );

				$connected_accounts =  isset( $options['connected_accounts'] ) ? $options['connected_accounts'] : array();
				$user_name = '';
				foreach ( $connected_accounts as $connected_account ) {
					if ( $connected_account['access_token'] === $error_connected_account['access_token'] ) {
						$connected_accounts[ $connected_account['user_id'] ]['is_valid'] = false;
						$connected_accounts[ $connected_account['user_id'] ]['last_checked'] = time();
						if ( isset( $connected_account['username'] ) ) {
							$user_name = $connected_account['username'];
						} else {
							$user_name = $connected_account['user_id'];
						}
					}
				}

				$options['connected_accounts'] = $connected_accounts;

				update_option( 'sb_instagram_settings', $options );
				$cap = current_user_can( 'manage_instagram_feed_options' ) ? 'manage_instagram_feed_options' : 'manage_options';
				$cap = apply_filters( 'sbi_settings_pages_capability', $cap );
				if ( (int)$response['error']['code'] === 100 && $error_connected_account['type'] === 'business' ) {
					$error = '<p><b>' . sprintf( __( 'Error: Permission for the Smash Balloon App is not valid or has expired.', 'instagram-feed' ), $user_name ) . ' ' . __( 'Feed will not update.', 'instagram-feed' ) . '</b></p>';
					$error .= '<p>' . sprintf( __( 'API error %s:', 'instagram-feed' ), $response['error']['code'] ) . ' ' . $response['error']['message'] . '</p>';
					$sb_instagram_posts_manager->add_error( 'at_100_' . $user_name, array( 'Business 100 Error', $error, $response['error']['code'] ) );
					$sb_instagram_posts_manager->add_frontend_error( 'at_100_' . $user_name, $error );

				} else {
					//set here
					$error = '<p><b>' . sprintf( __( 'Error: Access Token for %s is not valid or has expired.', 'instagram-feed' ), $user_name ) . ' ' . __( 'Feed will not update.', 'instagram-feed' ) . '</b></p>';
					$error .= '<p>' . __( 'There\'s an issue with the Instagram Access Token that you are using. Please obtain a new Access Token on the plugin\'s Settings page.', 'instagram-feed' );
					$error .= '<p>' . sprintf( __( 'API error %s:', 'instagram-feed' ), $response['error']['code'] ) . ' ' . $response['error']['message'] . '</p>';
					$sb_instagram_posts_manager->add_error( 'at_' . $user_name, array( 'Access Token Error', $error, $response['error']['code'] ) );
					$link = admin_url( '?page=sb-instagram-feed' );
					if ( current_user_can( $cap ) ) {
						$error .= '<p class="sbi-error-directions sbi-reconnect"><a href="' . esc_url( $link ) . '" target="_blank" rel="noopener">' . __( 'Reconnect your account in the admin area', 'instagram-feed' ) . '</a></p>';
					}
					$sb_instagram_posts_manager->add_frontend_error( 'at_' . $user_name, $error );
				}

				$error_time = 3600;
				$account_id = $error_connected_account['user_id'];
			} elseif ( (int)$response['error']['code'] === 18 ) {
				$options = get_option( 'sb_instagram_settings', array() );

				$connected_accounts =  isset( $options['connected_accounts'] ) ? $options['connected_accounts'] : array();
				$user_name = '';
				$hashtag_refresh_time = time() + (7*24*60*60);
				foreach ( $connected_accounts as $connected_account ) {
					if ( $connected_account['access_token'] === $error_connected_account['access_token'] ) {
						if ( ! isset( $connected_accounts[ $connected_account['user_id'] ]['hashtag_limit_reached'] ) ) {
							$connected_accounts[ $connected_account['user_id'] ]['hashtag_limit_reached'] = time();
						} else {
							$hashtag_refresh_time = $connected_accounts[ $connected_account['user_id'] ]['hashtag_limit_reached'];
						}
						if ( isset( $connected_account['username'] ) ) {
							$user_name = $connected_account['username'];
						} else {
							$user_name = $connected_account['user_id'];
						}
					}
				}

				$options['connected_accounts'] = $connected_accounts;

				update_option( 'sb_instagram_settings', $options );

				global $sb_instagram_posts_manager;
				$sb_instagram_posts_manager->add_error( 'error_18', array( 'Too many hashtags', $response['error']['error_user_msg'] ) );

			} elseif ( (int)$response['error']['code'] === 10 ) {
				$user_name = $error_connected_account['username'];

				$error = '<p><b>' . sprintf( __( 'Error: Connected account for the user %s does not have permission to use this feed type.', 'instagram-feed' ), $user_name ) .'</b></p>';
				$error .= '<p>' . __( 'Try using the big blue button on the "Configure" tab to reconnect the account and update its permissions.', 'instagram-feed' ) . '</p>';

				$sb_instagram_posts_manager->add_frontend_error( 'hashtag_limit_reached', $error );

			} elseif ( (int)$response['error']['code'] === 24 ) {
				$error = '<p><b>' . __( 'Error: Cannot retrieve posts for this hashtag.', 'instagram-feed' ) .'</b></p>';
				$error .= '<p>' . $response['error']['error_user_msg'] . '</p>';

				$sb_instagram_posts_manager->add_frontend_error( 'hashtag_error', $error );
			} else {

				$error = '<p>' .sprintf( __( 'API error %s:', 'instagram-feed' ), $response['error']['code'] ) . ' ' . $response['error']['message'] . '</p>';

				$sb_instagram_posts_manager->add_frontend_error( $response['error']['type'], $error );
				$sb_instagram_posts_manager->add_error( 'api', array( 'Api Error', $error, $response['error']['code'] ) );
			}

			if ( ! empty( $account_id ) ) {
				$sb_instagram_posts_manager->add_api_request_delay( $error_time, $account_id );
			} else {
				$sb_instagram_posts_manager->add_api_request_delay( $error_time );
			}

		}



	}

	/**
	 * Determines how and where to record an error connecting to a specified url
	 *
	 * @param $response
	 *
	 * @since 2.0/5.0
	 */
	public static function handle_wp_remote_get_error( $response ) {
		global $sb_instagram_posts_manager;
		delete_option( 'sbi_dismiss_critical_notice' );
		$sb_instagram_posts_manager->update_error_page( get_the_ID() );

		$message = sprintf( __( 'Error connecting to %s.', 'instagram-feed' ), $response['url'] ). ' ';
		if ( isset( $response['response'] ) && isset( $response['response']->errors ) ) {
			foreach ( $response['response']->errors as $key => $item ) {
				$message .= ' '.$key . ' - ' . $item[0] . ' |';
			}
		}

		$sb_instagram_posts_manager->add_api_request_delay( 300 );

		$sb_instagram_posts_manager->add_error( 'connection', array( 'Error connecting', $message ) );
	}

	/**
	 * Sets the url for the API request based on the account information,
	 * type of data needed, and additional parameters.
	 *
	 * Overwritten in the Pro version.
	 *
	 * @param array $connected_account connected account to be used in the request
	 * @param string $endpoint_slug header or user
	 * @param array $params additional params related to the request
	 *
	 * @since 2.0/5.0
	 * @since 2.2/5.3 added endpoints for the basic display API
	 */
	protected function set_url( $connected_account, $endpoint_slug, $params ) {
		$account_type = isset( $connected_account['type'] ) ? $connected_account['type'] : 'personal';
		$num = ! empty( $params['num'] ) ? (int)$params['num'] : 33;

		if ( $account_type === 'basic' ) {
			if ( $endpoint_slug === 'access_token' ) {
				$url = 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=' . sbi_maybe_clean( $connected_account['access_token'] );
			} elseif ( $endpoint_slug === 'header' ) {
				$url = 'https://graph.instagram.com/me?fields=id,username,media_count&access_token=' . sbi_maybe_clean( $connected_account['access_token'] );
			} else {
				$num = min( $num, 200 );
				$url = 'https://graph.instagram.com/' . sbi_maybe_clean( $connected_account['user_id'] ) . '/media?fields=media_url,thumbnail_url,caption,id,media_type,timestamp,username,comments_count,like_count,permalink,children{media_url,id,media_type,timestamp,permalink,thumbnail_url}&limit='.$num.'&access_token=' . sbi_maybe_clean( $connected_account['access_token'] );
			}
		} elseif ( $account_type === 'personal' ) {
			if ( $endpoint_slug === 'header' ) {
				$url = 'https://api.instagram.com/v1/users/' . $connected_account['user_id'] . '?access_token=' . sbi_maybe_clean( $connected_account['access_token'] );
			} else {
				$num = $num > 20 ? min( $num, 33 ) : 20; // minimum set at 20 due to IG TV bug
				$url = 'https://api.instagram.com/v1/users/' . $connected_account['user_id'] . '/media/recent?count='.$num.'&access_token=' . sbi_maybe_clean( $connected_account['access_token'] );
			}
		} else {
			if ( $endpoint_slug === 'header' ) {
				$url = 'https://graph.facebook.com/' . $connected_account['user_id'] . '?fields=biography,id,username,website,followers_count,media_count,profile_picture_url,name&access_token=' . sbi_maybe_clean( $connected_account['access_token'] );
			} else {
				$num = min( $num, 200 );
				$url = 'https://graph.facebook.com/' . $connected_account['user_id'] . '/media?fields=media_url,thumbnail_url,caption,id,media_type,timestamp,username,comments_count,like_count,permalink,children{media_url,id,media_type,timestamp,permalink,thumbnail_url}&limit='.$num.'&access_token=' . sbi_maybe_clean( $connected_account['access_token'] );
			}
		}

		$this->set_url_from_args( $url );
	}

}