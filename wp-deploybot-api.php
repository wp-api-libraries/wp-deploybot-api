<?php
/**
* WP DeployBot API
*
* @package WP-DeployBot-API
*/

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * DeployBot API Class.
 */
class DeployBotAPI {




	/**
	 * HTTP response code messages.
	 *
	 * @param  [String] $code : Response code to get message from.
	 * @return [String]       : Message corresponding to response code sent in.
	 */
	public function response_code_msg( $code = '' ) {
		switch ( $code ) {
			case 200:
				$msg = __( 'Success.','text-domain' );
				break;
			case 400:
				$msg = __( 'Bad Request: Malformed JSON payload.','text-domain' );
				break;
			case 401:
				$msg = __( 'Authentication Required: Missing or invalid API token.','text-domain' );
				break;
			case 403:
				$msg = __( 'Forbidden: Attempting to perform a restricted action.','text-domain' );
				break;
			case 422:
				$msg = __( 'Unprocessable Entity: Something is not right with the request data.','text-domain' );
				break;
			case 500:
				$msg = __( 'Internal Server Error: An error on our deploybot side. Please contact support if the error persists.','text-domain' );
				break;
			default:
				$msg = __( 'Response code unknown.', 'text-domain' );
				break;
		}
		return $msg;
	}

}
