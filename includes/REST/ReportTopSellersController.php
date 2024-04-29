<?php

namespace WeDevs\WePOS\REST;

/**
 * Report Order Totals API Controller
 */
class ReportTopSellersController extends \WC_REST_Report_Top_Sellers_Controller
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
    protected $base = 'reports/top_sellers';

    /**
     * Register the routes for reports sales.
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->base, array(
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_items1'),
                'permission_callback' => array($this, 'get_items_permissions_check'),
                'args'                => $this->get_collection_params(),
            ),
            'schema' => array($this, 'get_public_item_schema'),
        ));
    }

    /**
     * Get sales reports.
     *
     * @param WP_REST_Request $request
     * @return array|WP_Error
     */
    public function get_items1($request)
    {
        // Set date filtering.
        $filter = array(
            'period'   => $request['period'],
            'date_min' => $request['date_min'],
            'date_max' => $request['date_max'],
        );
        $this->setup_report($filter);

        $report_data = $this->report->get_order_report_data(array(
            'data' => array(
                '_product_id' => array(
                    'type'            => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function'        => '',
                    'name'            => 'product_id',
                ),
                '_qty' => array(
                    'type'            => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function'        => 'SUM',
                    'name'            => 'order_item_qty',
                ),
                '_line_total' => array(
                    'type'            => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function'        => 'SUM',
                    'name'            => 'order_item_total',
                ),
            ),
            'order_by'     => 'order_item_qty  DESC',
            'group_by'     => 'product_id',
            'limit'        => isset($filter['limit']) ? absint($filter['limit']) : 12,
            'query_type'   => 'get_results',
            'filter_range' => true,
        ));
        $top_sellers = array();

        foreach ($report_data as $item) {
            $product = wc_get_product($item->product_id);

            if ($product) {
                $top_sellers[] = array(
                    'name'      => $product->get_name(),
                    'sku'       => $product->get_sku(),
                    'product_id' => (int) $item->product_id,
                    'quantity'   => wc_stock_amount($item->order_item_qty),
                    'total'   => wc_stock_amount($item->order_item_total),
                );
            }
        }

        $data = array();
        foreach ($top_sellers as $top_seller) {
            // $item   = $this->prepare_item_for_response((object) $top_seller, $request);
            $data[] = $this->prepare_response_for_collection($top_seller);
        }

        return rest_ensure_response($data);
    }
}
