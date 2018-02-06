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

/* Add base class if needed. */
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

		/**
		 * Arguments to populate.
		 *
		 * @var array
		 */
		protected $args;

		/**
		 * Constructiroy.
		 *
		 * @access public
		 * @param string $subdomain The organization's subdomain.
		 * @param string $api_key   The auth API key.
		 * @return void
		 */
		public function __construct( string $subdomain, string $api_key ) {
			$this->set_api_key( $api_key );
			$this->set_subdomain( $subdomain );
		}

		/**
		 * Set the API key.
		 *
		 * @access public
		 * @param string $api_key The API key.
		 * @return void
		 */
		public function set_api_key( string $api_key ) {
			$this->api_key = $api_key;
		}

		/**
		 * Set the subdomian.
		 *
		 * @access public
		 * @param string $subdomain The subdomain.
		 * @return void
		 */
		public function set_subdomain( string $subdomain ) {
			$this->base_uri = 'https://' . $subdomain . '.deploybot.com/api/v1/';
		}

		/**
		 * Set headers.
		 *
		 * @access protected
		 * @return void
		 */
		protected function set_headers() {
			$this->args['headers'] = array(
				'X-Api-Token' => $this->api_key,
				'Accept'      => 'application/json',
			);
		}

		/**
		 * Clear arguments.
		 *
		 * @access protected
		 * @return void
		 */
		protected function clear() {
			$this->args = array();
		}

		/**
		 * Execute call. Is a wrapper for $this->build_request( $route, $body, $method )->fetch
		 *
		 * @access protected
		 * @param  string $route  The URI extension to send the request to.
		 * @param  array  $body   Additional arguments to pass.
		 * @param  string $method The method to call (get, delete, post, etc).
		 * @return mixed          The response.
		 */
		protected function run( string $route, array $body = array(), string $method = 'GET' ) {
			return $this->build_request( $route, $body, $method )->fetch();
		}

		/**
		 * Parse an array of arguments for nullity, and keep non-null ones.
		 *
		 * @access private
		 * @param  array $args  Array/key arguments to parse.
		 * @param  array $merge (Default: array()) An array of additional arguments to merge.
		 * @return array        The cleansed/merged array.
		 */
		private function parse_args( $args, $merge = array() ) {
			$results = array();

			foreach ( $args as $key => $val ) {
				if ( null !== $val ) {
					$results[ $key ] = $val;
				} else if ( is_array( $val ) && ! empty( $val ) ) {
					$results[ $key ] = $val;
				}
			}

			return array_merge( $merge, $results );
		}

		/**
		 * Get users.
		 *
		 * @access public
		 * @param int   $limit (Default: 50) The number of posts to limit the result to.
		 * @param mixed $after (Default: null) The post to display results following.
		 * @return object
		 */
		function get_users( $limit = 50, $after = null ) {
			$args = $this->parse_args( array(
				'limit' => intval( $limit ),
				'after' => $after,
			));

			return $this->run( 'users', $args );
		}


		/**
		 * Get a single user.
		 *
		 * @access public
		 * @param mixed $user_id The user to grab.
		 * @return object        The user.
		 */
		function get_user( $user_id ) {
			return $this->run( 'users/' . $user_id );
		}

		/**
		 * List deployments.
		 *
		 * @access public
		 * @param mixed $repository_id  The repository to display from.
		 * @param mixed $environment_id The environment to display from.
		 * @param int   $limit          (Default: 50) The number of results to list.
		 * @param mixed $after          (Default: null) The environment to display results after.
		 * @return object               An object containing the entries, and some pagination properties.
		 */
		function list_deployments( $repository_id, $environment_id, int $limit = 50, $after = null ) {
			$args = $this->parse_args( array(
				'repository_id'  => $repository_id,
				'environment_id' => $environment_id,
				'limit'          => $limit,
				'after'          => $after,
			));

			return $this->run( 'deployments', $args );
		}


		/**
		 * Get a deployment.
		 *
		 * @access public
		 * @param mixed $deployment_id The deployment ID to get.
		 * @return object              The deployment
		 */
		function get_deployment( $deployment_id ) {
			return $this->run( 'deployments/'.$deployment_id );
		}


		/**
		 * Trigger a deployment.
		 *
		 * @access public
		 * @param mixed $environment_id The ID of the environment to trigger.
		 * @param array $args           Additional arguments, supports:
		 *                                user_id (default -> account owner)
		 *                                deployed_version (git commit)
		 *                                deploy_from_scratch (default false)
		 *                                trigger_notifications (default true)
		 *                                comment.
		 * @return object               The result of the deployment.
		 */
		function trigger_deployment( $environment_id, $args = array() ) {
			$args['environment_id'] = $environment_id;
			return $this->run( 'deployments', $args, 'POST' );
		}

		/**
		 * Get repository.
		 *
		 * @access public
		 * @param  mixed $repository_id The ID of a repository to grab.
		 * @return object               The repository.
		 */
		function get_repository( $repository_id ) {
			return $this->run( 'repositories/'.$repository_id );
		}

		/**
		 * List repositories.
		 *
		 * @access public
		 * @param  int   $limit (default: 50) The number of results to grab.
		 * @param  mixed $after (default: null) The result to display results following.
		 * @return object        An object containing the entries, and some pagination properties.
		 */
		function list_repositories( int $limit = 50, $after = null ) {
			$args = $this->parse_args( array(
				'limit' => $limit,
				'after' => $after,
			));

			return $this->run( 'repositories', $args );
		}

		/**
		 * Get an environment.
		 *
		 * @access public
		 * @param  mixed $environment_id The ID of the environment to get.
		 * @return object                The environment.
		 */
		function get_environment( $environment_id ) {
			return $this->run( 'environment/'.$environment_id );
		}

		/**
		 * List environments.
		 *
		 * @access public
		 * @param  mixed $repository_id (Default: null) The ID of the repository to limit results by.
		 * @param  int   $limit         (Default: 50) The number of environments to display.
		 * @param  mixed $after         (Default: null) The post to list following.
		 * @return object               An object containing the entries, and some pagination properties.
		 */
		function list_environments( $repository_id = null, int $limit = 50, $after = null ) {
			$args = $this->parse_args( array(
				'repository_id' => $repository_id,
				'limit'         => 50,
				'after'         => $after,
			));

			return $this->run( 'environments', $args );
		}

		/**
		 * Get server details.
		 *
		 * @access public
		 * @param  mixed $server_id The ID of the server.
		 * @return object           The server
		 */
		function get_server( $server_id ) {
			return $this->run( 'server/'.$server_id );
		}


		/**
		 * List servers.
		 *
		 * @access public
		 * @param  mixed $repository_id  (Default: null) The repository to limit results to.
		 * @param  mixed $environment_id (Default: null) The environment to limit results to.
		 * @param  int   $limit          (Default: 50) The number of environments to display.
		 * @param  mixed $after          (Default: null) The post to list following.
		 * @return object                 An object containing the entries, and some pagination properties.
		 */
		function list_servers( $repository_id = null, $environment_id = null, int $limit = 50, $after = null ) {
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
		 * @access public
		 * @param  mixed $code Response code to get message from.
		 * @return string       Message corresponding to response code sent in.
		 */
		public function response_code_msg( $code = '' ) {
			switch ( $code ) {
				case 200:
					$msg = __( 'Success.', 'wp-deploy-bot' );
				break;
				case 400:
					$msg = __( 'Bad Request: Malformed JSON payload.', 'wp-deploy-bot' );
				break;
				case 401:
					$msg = __( 'Authentication Required: Missing or invalid API token.', 'wp-deploy-bot' );
				break;
				case 403:
					$msg = __( 'Forbidden: Attempting to perform a restricted action.', 'wp-deploy-bot' );
				break;
				case 422:
					$msg = __( 'Unprocessable Entity: Something is not right with the request data.', 'wp-deploy-bot' );
				break;
				case 500:
					$msg = __( 'Internal Server Error: An error on our deploybot side. Please contact support if the error persists.', 'wp-deploy-bot' );
				break;
				default:
					$msg = __( 'Response code unknown.', 'wp-deploy-bot' );
				break;
			}
			return $msg;
		}
	}
}
