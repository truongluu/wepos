<?php

namespace WeDevs\WePOS\REST;

/**
 * Customer API Controller
 */
class CustomerController extends \WC_REST_Customers_Controller
{

    /**
     * Endpoint namespace
     *
     * @var string
     */
    protected $namespace = 'wepos/v1';

    /**
     * Route name
     *
     * @var string
     */
    protected $base = 'customers';

    /**
     * Register the routes for customers.
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->base, array(
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_customers'),
                'permission_callback' => array($this, 'get_customers_permissions_check'),
                'args'                => $this->get_collection_params(),
            ),
            array(
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'create_customer'),
                'permission_callback' => array($this, 'create_customer_permission_callback'),
                'args'                => array_merge($this->get_endpoint_args_for_item_schema(\WP_REST_Server::CREATABLE), array(
                    'email' => array(
                        'required' => true,
                        'type'     => 'string',
                        'description' => __('New user email address.', 'wepos'),
                    ),
                    'username' => array(
                        'required' => 'no' === get_option('woocommerce_registration_generate_username', 'yes'),
                        'description' => __('New user username.', 'wepos'),
                        'type'     => 'string',
                    ),
                    'password' => array(
                        'required' => 'no' === get_option('woocommerce_registration_generate_password', 'no'),
                        'description' => __('New user password.', 'wepos'),
                        'type'     => 'string',
                    ),
                )),
            ),

            'schema' => array($this, 'get_public_item_schema'),
        ));
        register_rest_route($this->namespace, '/' . $this->base . '/(?P<id>[\d]+)', array(
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_item'),
                'permission_callback' => array($this, 'get_item_permissions_check'),
                'args'                => array(
                    'context' => $this->get_context_param(array('default' => 'view')),
                ),
            ),
            array(
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'update_customer'),
                'permission_callback' => array($this, 'update_customer_permission_callback'),
                'args'                => array_merge($this->get_endpoint_args_for_item_schema(\WP_REST_Server::EDITABLE), array(
                    'email' => array(
                        'required' => true,
                        'type'     => 'string',
                        'description' => __('New user email address.', 'wepos'),
                    ),
                )),
            ),
            array(
                'methods'             => \WP_REST_Server::DELETABLE,
                'callback'            => array($this, 'delete_item'),
                'permission_callback' => array($this, 'delete_item_permissions_check'),
                'args'                => array(
                    'force' => array(
                        'default'     => false,
                        'type'        => 'boolean',
                        'description' => __('Required to be true, as resource does not support trashing.', 'woocommerce'),
                    ),
                    'reassign' => array(
                        'default'     => 0,
                        'type'        => 'integer',
                        'description' => __('ID to reassign posts to.', 'woocommerce'),
                    ),
                ),
            ),
            'schema' => array($this, 'get_public_item_schema'),
        ));
    }

    /**
     * Get customers permission checking
     *
     * @since 1.0.2
     *
     * @return bool|\WP_Error
     */
    public function create_customer_permission_callback()
    {
        if (!(current_user_can('manage_woocommerce') || apply_filters('wepos_rest_manager_permissions', false))) {
            return new \WP_Error('wepos_rest_cannot_batch', __('Sorry, you are not allowed view this resource.', 'wepos'), array('status' => rest_authorization_required_code()));
        }

        return true;
    }

    /**
     * Update customers permission checking
     *
     * @since 1.0.2
     *
     * @return bool|\WP_Error
     */
    public function update_customer_permission_callback()
    {
        if (!(current_user_can('manage_woocommerce') || apply_filters('wepos_rest_manager_permissions', false))) {
            return new \WP_Error('wepos_rest_cannot_batch', __('Sorry, you are not allowed view this resource.', 'wepos'), array('status' => rest_authorization_required_code()));
        }

        return true;
    }
    /**
     * Get customers permission checking
     *
     * @since 1.0.2
     *
     * @return bool|\WP_Error
     *
     */
    public function get_customers_permissions_check()
    {
        if (!(current_user_can('manage_woocommerce') || apply_filters('wepos_rest_manager_permissions', false))) {
            return new \WP_Error('wepos_rest_cannot_batch', __('Sorry, you are not allowed view this resource.', 'wepos'), array('status' => rest_authorization_required_code()));
        }

        return true;
    }

    /**
     * Create a customer
     *
     * @since 1.0.5
     *
     * @return \WP_Error|\WP_REST_Response
     */
    public function create_customer($request)
    {
        return $this->create_item($request);
    }

    /**
     * Update a customer
     *
     * @since 1.0.5
     *
     * @return \WP_Error|\WP_REST_Response
     */
    public function update_customer($request)
    {
        return $this->update_item($request);
    }

    /**
     * Get customers
     *
     * @since 1.0.5
     *
     * @return \WP_Error|\WP_REST_Response
     */
    public function get_customers($request)
    {
        return $this->get_items($request);
    }
}
