<?php
/**
 * WP DeployBot API
 *
 * @package WP-DeployBot-API
 */

/*
* Plugin Name: WP Deploybot API
* Plugin URI: https://github.com/wp-api-libraries/wp-deploybot-api
* Description: Perform API requests to Deploybot in WordPress.
* Author: WP API Libraries
* Version: 1.0.0
* Author URI: https://wp-api-libraries.com
* GitHub Plugin URI: https://github.com/wp-api-libraries/wp-deploybot-api
* GitHub Branch: master
*/

/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WpDeployBotBase' ) ) {
	include_once( 'wp-api-libraries-base.php' );
}

/* Check if class exists. */
if ( ! class_exists( 'DeployBotAPI' ) ) {

	/**
	 * DeployBot API Class.
	 */
	class DeployBotAPI extends WpDeployBotBase {

		/**
		 * API Token.
		 *
		 * @var string
		 */
		private $api_token;

		/**
		 * URL to the API.
		 *
		 * @var string
		 */
		protected $base_uri;

		protected $args;

		public function __construct( $subdomain, $api_key ) {
			$this->set_api_key( $api_key );
			$this->set_subdomain( $subdomain );
		}

		public function set_api_key( $api_key ) {
			$this->api_key = $api_key;
		}

		public function set_subdomain( $subdomain ) {
			$this->base_uri = 'https://' . $subdomain . '.deploybot.com/api/v1/';
		}

		protected function set_headers() {
			$this->args['headers'] = array(
				'X-Api-Token' => $this->api_key,
				'Accept'      => 'application/json',
			);
		}

		protected function clear() {
			$this->args = array();
		}

		protected function run( $route, $body = array(), $method = 'GET' ) {
			return $this->build_request( $route, $body, $method )->fetch();
		}

		private function parse_args( $args, $merge = array() ) {
			$results = array();

			foreach ( $args as $key => $val ) {
				if ( $val !== null ) {
					$results[ $key ] = $val;
				} else if ( is_array( $val ) && ! empty( $val ) ) {
					$results[ $key ] = $val;
				}
			}

			return array_merge( $merge, $results );
		}

		/**
		 * get_users function.
		 *
		 * @access public
		 * @return void
		 */
		function get_users( $limit = 50, $after = null ) {
			$args = $this->parse_args( array(
				'limit' => intval( $limit ),
				'after' => $after,
			));

			return $this->run( 'users', $args );
		}


		/**
		 * get_user function.
		 *
		 * @access public
		 * @param mixed $user_id
		 * @return void
		 */
		function get_user( $user_id ) {
			return $this->run( 'users/' . $user_id );
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
		function list_deployments( $repository_id, $environment_id, $limit = 50, $after = null ) {
			$args = $this->parse_args( array(
				'repository_id'  => $repository_id,
				'environment_id' => $environment_id,
				'limit'          => $limit,
				'after'          => $after,
			));

			return $this->run( 'deployments', $args );
		}


		/**
		 * Get a deployment
		 *
		 * @access public
		 * @param mixed $deployment_id
		 * @return void
		 */
		function get_deployment( $deployment_id ) {
			return $this->run( 'deployments/'.$deployment_id );
		}


		/**
		 * trigger_deployment function.
		 *
		 * @access public
		 * @param mixed $environment_id
		 * @param array $args           Additional arguments, supports:
		 *                                user_id (default -> account owner)
		 *                                deployed_version (git commit)
		 *                                deploy_from_scratch (default false)
		 *                                trigger_notifications (default true)
		 *                                comment
		 * @return void
		 */
		function trigger_deployment( $environment_id, $args = array() ) {
			$args['environment_id'] = $environment_id;
			return $this->run( 'deployments', $args, 'POST' );
		}

		/**
		 * Get Repository.
		 *
		 * @access public
		 * @return void
		 */
		function get_repository( $repository_id ) {
			return $this->run( 'repositories/'.$repository_id );
		}

		/**
		 * List Repositories
		 *
		 * @access public
		 * @param string $limit (default: '')
		 * @param string $after (default: '')
		 * @return void
		 */
		function list_repositories( $limit = 50, $after = null ) {
			$args = $this->parse_args( array(
				'limit' => $limit,
				'after' => $after,
			));

			return $this->run( 'repositories', $args );
		}

		/**
		 * get_environment function.
		 *
		 * @access public
		 * @return void
		 */
		function get_environment( $environment_id ) {
			return $this->run( 'environment/'.$environment_id );
		}

		/**
		 * List Environments.
		 *
		 * @access public
		 * @param mixed $limit
		 * @param mixed $after
		 * @return void
		 */
		function list_environments( $repository_id = null, $limit = 50, $after = null ) {
			$args = $this->parse_args( array(
				'repository_id' => $repository_id,
				'limit'         => 50,
				'after'         => $after,
			));

			return $this->run( 'environments', $args );
		}

		/**
		 * Get Server Details.
		 *
		 * @access public
		 * @return void
		 */
		function get_server( $server_id ) {
			return $this->run( 'server/'.$server_id );
		}


		/**
		 * List Servers.
		 *
		 * @access public
		 * @param mixed $limit Limit.
		 * @param mixed $after After.
		 * @return void
		 */
		function list_servers( $repository_id = null, $environment_id = null, $limit = 50, $after = null ) {
			$args = $this->parse_args(array(
				'repository_id'  => $repository_id,
				'environment_id' => $environment_id,
				'limit'          => $limit,
				'after'          => $after,
			));

			return $this->run( 'servers', $args );
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
