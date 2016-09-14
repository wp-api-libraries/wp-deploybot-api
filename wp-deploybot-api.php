<?php
/**
 * WP DeployBot API
 *
 * @package WP-DeployBot-API
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) { exit; }


if ( ! class_exists( 'DeployBotAPI' ) ) {

	/**
	 * DeployBot API Class.
	 */
	class DeployBotAPI {

		/**
		 * API Token.
		 *
		 * @var string
		 */
		static private $api_token;

		/**
		 * Account Subdomain
		 *
		 * @var string
		 */
		static private $account_subdomain;

		/**
		 * URL to the API.
		 *
		 * @var string
		 */
		private $base_uri = 'https://' . static::$account_subdomain . '.deploybot.com/api/v1';

		/**
		 * Fetch the request from the API.
		 *
		 * @access private
		 * @param mixed $request Request URL.
		 * @return $body Body.
		 */
		private function fetch( $request ) {

			$request .= '?token=' .static::$api_token;
			$response = wp_remote_get( $request );

			$code = wp_remote_retrieve_response_code( $response );

			if ( 200 !== $code ) {
				return new WP_Error( 'response-error', sprintf( __( 'Server response code: %d', 'text-domain' ), $code ) );
			}

			$body = wp_remote_retrieve_body( $response );
			return json_decode( $body );
		}


		/**
		 * get_users function.
		 *
		 * @access public
		 * @return void
		 */
		function get_users() {

			$request = $this->base_uri . '/users';
			return $this->fetch( $request );

		}


		/**
		 * get_user function.
		 *
		 * @access public
		 * @param mixed $user_id
		 * @return void
		 */
		function get_user( $user_id ) {

			if ( empty( $user_id ) ) {
				return new WP_Error( 'required-fields', __( 'Required fields are empty.', 'text-domain' ) );
			}

			$request = $this->base_uri . '/users/' . $user_id;
			return $this->fetch( $request );

		}

		/**
		 * List deployments.
		 *
		 * @access public
		 * @param mixed $repository_id
		 * @param mixed $environment_id
		 * @param mixed $limit
		 * @param mixed $after
		 * @return void
		 */
		function list_deployments( $repository_id, $environment_id, $limit, $after ) {

			$request = $this->base_uri . '/deployments';
			return $this->fetch( $request );

		}


		/**
		 * Get a deployment
		 *
		 * @access public
		 * @param mixed $deployment_id
		 * @return void
		 */
		function get_deployment( $deployment_id ) {

			$request = $this->base_uri . '/deployments/' . $deployment_id;
			return $this->fetch( $request );

		}


		/**
		 * trigger_deployment function.
		 *
		 * @access public
		 * @param mixed $environment_id
		 * @return void
		 */
		function trigger_deployment( $environment_id ) {

			$request = $this->base_uri . '/deployments';
			return $this->fetch( $request );

		}

		function get_repository() {

		}

		function list_repositories() {

		}

		function get_environment() {

		}

		function list_environments() {

		}

		function get_server() {

		}

		function list_servers() {

		}

		/**
		 * HTTP response code messages.
		 *
		 * @param  [String] $code : Response code to get message from.
		 * @return [String]       : Message corresponding to response code sent in.
		 */
		public function response_code_msg( $code = '' ) {
			switch ( $code ) {
			case 200:
				$msg = __( 'Success.', 'text-domain' );
				break;
			case 400:
				$msg = __( 'Bad Request: Malformed JSON payload.', 'text-domain' );
				break;
			case 401:
				$msg = __( 'Authentication Required: Missing or invalid API token.', 'text-domain' );
				break;
			case 403:
				$msg = __( 'Forbidden: Attempting to perform a restricted action.', 'text-domain' );
				break;
			case 422:
				$msg = __( 'Unprocessable Entity: Something is not right with the request data.', 'text-domain' );
				break;
			case 500:
				$msg = __( 'Internal Server Error: An error on our deploybot side. Please contact support if the error persists.', 'text-domain' );
				break;
			default:
				$msg = __( 'Response code unknown.', 'text-domain' );
				break;
			}
			return $msg;
		}

	}
}
