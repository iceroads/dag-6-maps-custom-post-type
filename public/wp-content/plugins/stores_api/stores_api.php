<?php
/*
Plugin Name: Stores API
Plugin URI: https://securewp.test
Description: Best Mother F API endpoints Evever! MURICA!!!
Version: 1.0.0
Author: WCMS18
Author URI: https://google.se
License: GPLv2 or later
Text Domain: stores
*/


class My_REST_Posts_Controller
{

// Here initialize our namespace and resource name.
    public function __construct()
    {
        $this->namespace = '/stores_api/v1';
        $this->resource_name = 'stores';
    }

// Register our routes.
    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->resource_name, array(
// Here we register the readable endpoint for collections.
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_items'),
                'permission_callback' => array($this, 'get_items_permissions_check')
            ),
// Register our schema callback.
            'schema' => array($this, 'get_item_schema'),
        ));
    }

    /**
     * Check permissions for the posts.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_items_permissions_check($request)
    {
        return wp_verify_nonce($request->get_param('_wpnonce'), 'wp_rest');
    }

    /**
     * Grabs the five most recent posts and outputs them as a rest response.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_items($request)
    {
        $args = array(
            'post_per_page' => -1,
            'post_type' => 'store',
        );
        $posts = get_posts($args);

        $data = array();

        if (empty($posts)) {
            return rest_ensure_response($data);
        }

        foreach ($posts as $post) {
            $response = $this->prepare_item_for_response($post, $request);
            $data[] = $this->prepare_response_for_collection($response);
        }

        // Return all of our comment response data.
        return rest_ensure_response($data);
    }

    /**
     * Check permissions for the posts.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_item_permissions_check($request)
    {
        if (!current_user_can('read')) {
            return new WP_Error('rest_forbidden', esc_html__('You cannot view the post resource.'), array('status' => $this->authorization_status_code()));
        }
        return true;
    }

    /**
     * Matches the post data to the schema we want.
     *
     * @param WP_Post $post The comment object whose response is being prepared.
     */
    public function prepare_item_for_response($post, $request)
    {
        $post_data = array();

        $schema = $this->get_item_schema($request);

        // We are also renaming the fields to more understandable names.
        if (isset($schema['properties']['id'])) {
            $post_data['id'] = (int)$post->ID;
        }

        if (isset($schema['properties']['title'])) {
            $post_data['title'] = $post->post_title;
        }

        return rest_ensure_response($post_data);
    }

    /**
     * Prepare a response for inserting into a collection of responses.
     *
     * This is copied from WP_REST_Controller class in the WP REST API v2 plugin.
     *
     * @param WP_REST_Response $response Response object.
     * @return array Response data, ready for insertion into collection data.
     */
    public function prepare_response_for_collection($response)
    {
        if (!($response instanceof WP_REST_Response)) {
            return $response;
        }

        $data = (array)$response->get_data();
        $server = rest_get_server();

        if (method_exists($server, 'get_compact_response_links')) {
            $links = call_user_func(array($server, 'get_compact_response_links'), $response);
        } else {
            $links = call_user_func(array($server, 'get_response_links'), $response);
        }

        if (!empty($links)) {
            $data['_links'] = $links;
        }

        return $data;
    }

    /**
     * Get our sample schema for a post.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_item_schema($request)
    {
        $schema = array(
            // This tells the spec of JSON Schema we are using which is draft 4.
            '$schema' => 'http://json-schema.org/draft-04/schema#',
            // The title property marks the identity of the resource.
            'title' => 'post',
            'type' => 'object',
            // In JSON Schema you can specify object properties in the properties attribute.
            'properties' => array(
                'id' => array(
                    'description' => esc_html__('Unique identifier for the object.', 'my-textdomain'),
                    'type' => 'integer',
                    'context' => array('view', 'edit', 'embed'),
                    'readonly' => true,
                ),
                'title' => array(
                    'description' => esc_html__('The title for the object.', 'my-textdomain'),
                    'type' => 'string',
                ),
            ),
        );

        return $schema;
    }

    // Sets up the proper HTTP status code for authorization.
    public function authorization_status_code()
    {

        $status = 401;

        if (is_user_logged_in()) {
            $status = 403;
        }

        return $status;
    }
}

// Function to register our new routes from the controller.
function prefix_register_my_rest_routes()
{
    $controller = new My_REST_Posts_Controller();
    $controller->register_routes();
}

add_action('rest_api_init', 'prefix_register_my_rest_routes');

function endpoint_js() {
    wp_enqueue_script('endpoints', plugins_url( '/stores_api.js', __FILE__ ));
    wp_localize_script('endpoints', 'store_plugin', [
        "wp_store_endpoint" => esc_url_raw(rest_url('/stores_api/v1/stores')),
        "wp_rest_nonce" => wp_create_nonce('wp_rest')
    ]);
};

add_action('wp_enqueue_scripts', 'endpoint_js');










