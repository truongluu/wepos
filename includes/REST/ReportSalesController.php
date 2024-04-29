<?php

namespace WeDevs\WePOS\REST;

/**
 * Report Sales API Controller
 */
class ReportSalesController extends \WC_REST_Report_Sales_Controller
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
    protected $base = 'reports/sales';

    /**
     * Register the routes for reports sales.
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->base, array(
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_items'),
                'permission_callback' => array($this, 'get_items_permissions_check'),
                'args'                => $this->get_collection_params(),
            ),
            'schema' => array($this, 'get_public_item_schema'),
        ));
    }
}
