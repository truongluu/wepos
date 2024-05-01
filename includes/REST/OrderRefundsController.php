<?php

namespace WeDevs\WePOS\REST;

/**
 * Order Refunds API Controller
 */
class OrderRefundsController extends \WC_REST_Order_Refunds_Controller
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
    protected $base = 'orders/(?P<order_id>[\d]+)/refunds';

    /**
     * Register the routes for orders.
     */
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->base,
            array(
                'args'   => array(
                    'order_id' => array(
                        'description' => __('The order ID.', 'woocommerce'),
                        'type'        => 'integer',
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array($this, 'create_item'),
                    'permission_callback' => array($this, 'create_item_permissions_check'),
                    'args'                => $this->get_endpoint_args_for_item_schema(\WP_REST_Server::CREATABLE),
                ),
                'schema' => array($this, 'get_public_item_schema'),
            )
        );
    }
}
