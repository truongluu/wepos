<template>
    <div class="page page__wrapper">
        <h1 class="page__title">{{ __("Orders", "wepos") }}</h1>
        <filter-order @onOrderFilterSelected="selectProductFilter" />
        <ve-table
            :columns="columns"
            :table-data="tableData"
            :columnHiddenOption="columnHiddenOption"
            max-height="max(calc(100vh - 200px), 400px)"
        />
        <div class="table-pagination">
            <ve-pagination
                :total="totalPages"
                :page-index="pageIndex"
                :page-size="pageSize"
                @on-page-number-change="pageNumberChange"
                @on-page-size-change="pageSizeChange"
            />
        </div>
        <div v-show="!orderLoading && showEmpty" class="empty-data">
            {{ __("Data Empty", "wepos") }}.
        </div>
    </div>
</template>

<script>
import { DEFAULT_PAGE_SIZE, ORDER_STATUS } from "@/const";

import { __ } from "@/utils/i18n";
import FilterOrder from "./FilterOrder.vue";
import ActionsButton from "./OrderActionsButton.vue";

export default {
    name: "Orders",
    components: { FilterOrder, ActionsButton },
    data() {
        return {
            orderByKey: {},
            selectedOrderId: 0,
            orderStatus: "any",
            columnHiddenOption: {
                defaultHiddenColumnKeys: ["id"],
            },
            columns: [
                {
                    field: "id",
                    key: "id",
                    title: "",
                },
                {
                    field: "",
                    key: "index",
                    title: "#",
                    renderBodyCell: ({ rowIndex }) => {
                        return `${++rowIndex}`;
                    },
                },
                {
                    field: "order",
                    key: "a",
                    title: __("Order", "wepos"),
                    width: 200,
                    align: "left",
                },
                {
                    field: "total",
                    key: "b",
                    title: __("Total", "wepos"),
                    align: "left",
                },
                {
                    field: "status",
                    key: "c",
                    title: __("Status", "wepos"),
                    align: "left",
                    renderBodyCell: ({ row }) => {
                        return `${ORDER_STATUS[row["status"]]}`;
                    },
                },
                {
                    field: "customer",
                    key: "d",
                    title: __("Customer", "wepos"),
                    align: "left",
                },
                {
                    field: "gateway",
                    key: "e",
                    title: __("Gateway", "wepos"),
                    align: "left",
                },
                {
                    field: "date",
                    key: "f",
                    title: __("Date", "wepos"),
                    align: "left",
                },
                {
                    field: "",
                    key: "actions",
                    title: "",
                    width: 80,
                    renderBodyCell: ({ row }, h) => {
                        return h(ActionsButton, {
                            props: {
                                actionId: row["id"],
                                status: row["status"],
                            },
                            on: {
                                onEditAction: this.editOrder,
                                onRefundAction: this.refundOrder,
                            },
                        });
                    },
                },
            ],
            tableData: [],
            page: 1,
            orderLoading: false,
            showEmpty: false,
            pageIndex: 1,
            pageSize: DEFAULT_PAGE_SIZE,
            totalPages: 0,
        };
    },
    methods: {
        confirmRefundOrder(orderId) {
            this.showLoading();
            const currentOrder = this.orderByKey[orderId];
            if (currentOrder) {
                wepos.api
                    .post(
                        wepos.rest.root +
                            wepos.rest.posversion +
                            `/orders/${orderId}/refunds`,
                        {
                            api_refund: false,
                            amount: currentOrder.total,
                        }
                    )
                    .done(() => {
                        this.startFetchOrders(1);
                        this.hideLoading();
                    })
                    .always(() => {
                        this.hideLoading();
                    });
            }
        },
        editOrder(orderId) {
            this.$router.push(`/orders/${orderId}`);
        },
        refundOrder(orderId) {
            this.selectedOrderId = orderId;
            this.confirmAlert({
                title: this.__("Are you sure", "wepos"),
                text: this.__("You want to refund this order?", "wepos"),
            }).then((result) => {
                if (result.isConfirmed) {
                    this.confirmRefundOrder(orderId);
                }
            });
        },
        reopenOrder(orderId) {
            const orderData = this.orderByKey[orderId];
            const {
                billing,
                customer_id,
                customer_note,
                payment_method,
                payment_method_title,
                shipping,
                fee_lines,
                line_items,
            } = orderData;
            if (line_items?.length) {
                const productIds = line_items.map((item) => item.product_id);
                this.showLoading();
                wepos.api
                    .get(
                        wepos.rest.root +
                            wepos.rest.posversion +
                            "/products?include=" +
                            productIds.toString()
                    )
                    .then((response) => {
                        for (const productItem of response) {
                            const productFromOrder = line_items.find(
                                (lineItem) =>
                                    lineItem.product_id === productItem.id
                            );

                            if (productItem?.type === "variable") {
                                const selectedAttribute =
                                    productFromOrder?.meta_data?.reduce(
                                        (result, metaItem) => {
                                            if (
                                                metaItem.display_key &&
                                                metaItem.display_value
                                            ) {
                                                result = {
                                                    ...result,
                                                    [metaItem.display_key]:
                                                        metaItem.display_value,
                                                };
                                            }
                                            return result;
                                        },
                                        {}
                                    ) || {};

                                const chosenVariationProduct =
                                    this.findMatchingVariations(
                                        productItem.variations,
                                        selectedAttribute
                                    );
                                const variationProduct =
                                    chosenVariationProduct[0];
                                if (!this.hasStock(variationProduct)) {
                                    this.toast({
                                        title: this.__(
                                            "This product is out of stock.",
                                            "wepos"
                                        ),
                                        type: "error",
                                    });
                                }

                                variationProduct.parent_id = productItem.id;
                                variationProduct.type = productItem.type;
                                variationProduct.name = productItem.name;
                                variationProduct.type = productItem.type;
                                this.$store.dispatch(
                                    "Cart/addToCartActionWithQuantity",
                                    {
                                        product: variationProduct,
                                        quantity:
                                            productFromOrder?.quantity || 1,
                                        line_item_id: productFromOrder?.id || 0,
                                    }
                                );
                            } else {
                                this.$store.dispatch(
                                    "Cart/addToCartActionWithQuantity",
                                    {
                                        product: productItem,
                                        quantity:
                                            productFromOrder?.quantity || 1,
                                        line_item_id: productFromOrder?.id || 0,
                                    }
                                );
                            }
                        }
                        if (typeof localStorage != "undefined") {
                            localStorage.setItem(
                                "currentOrderId",
                                `${orderId}`
                            );
                            localStorage.setItem(
                                "cartdata",
                                JSON.stringify({
                                    ...this.$store.state.Cart.cartdata,
                                    fee_lines,
                                })
                            );
                            localStorage.setItem(
                                "holdCartdata",
                                JSON.stringify({
                                    ...this.$store.state.Cart.cartdata,
                                    fee_lines,
                                })
                            );
                            localStorage.setItem(
                                "orderdata",
                                JSON.stringify({
                                    billing,
                                    customer_id,
                                    customer_note,
                                    payment_method,
                                    payment_method_title,
                                    shipping,
                                })
                            );
                        }
                    })
                    .always(() => {
                        this.hideLoading();
                    });
            }

            this.$router.push({ name: "Home" });
            this.success({
                title: this.__("Reopen order successfully", "wepos"),
            });
        },

        startFetchOrders(page) {
            if (page) {
                this.page = page;
            }
            this.tableData = [];
            this.fetchOrders();
        },
        pageNumberChange(pageIndex) {
            this.page = pageIndex;
            this.startFetchOrders();
        },
        pageSizeChange(pageSize) {
            this.page = 1;
            this.pageSize = pageSize;
            this.startFetchOrders();
        },

        selectProductFilter(orderStatus) {
            this.orderStatus = orderStatus;
            this.page = 1;
            this.tableData = [];
            this.fetchOrders();
        },
        appendOrders(orders) {
            orders.forEach((order) => {
                const orderData = {
                    id: order.id,
                    order: `Order #${order.id}`,
                    total: this.formatPrice(order.total),
                    status: order.status,
                    date: this.formatDate(order.date_created),
                    gateway: order.payment_method_title,
                    customer: order.customer_id
                        ? `${order.billing.first_name} ${order.billing.last_name}`
                        : this.__("Guest user", "wepos"),
                };
                this.tableData = this.tableData.concat(orderData);
                this.orderByKey[order.id] = order;
            });
            if (this.tableData.length === 0) {
                this.showEmpty = true;
            } else {
                this.showEmpty = false;
            }
        },
        fetchOrders() {
            this.showLoading();
            this.orderLoading = true;
            wepos.api
                .get(
                    wepos.rest.root +
                        wepos.rest.posversion +
                        `/orders?per_page=${this.pageSize}&page=${this.page}&status=${this.orderStatus}`
                )
                .done((response, status, xhr) => {
                    this.appendOrders(response);
                    this.page += 1;
                    this.totalPages = parseInt(
                        xhr.getResponseHeader("X-WP-TotalPages")
                    );
                })
                .always(() => {
                    this.hideLoading();
                    this.orderLoading = false;
                });
        },
    },
    created() {
        this.columns = [
            {
                field: "id",
                key: "id",
                title: "",
            },
            {
                field: "",
                key: "index",
                title: "#",
                renderBodyCell: ({ rowIndex }) => {
                    return `${++rowIndex}`;
                },
            },
            {
                field: "order",
                key: "a",
                title: this.__("Order", "wepos"),
                width: 200,
                align: "left",
            },
            {
                field: "total",
                key: "b",
                title: this.__("Total", "wepos"),
                align: "left",
            },
            {
                field: "status",
                key: "c",
                title: this.__("Status", "wepos"),
                align: "left",
                renderBodyCell: ({ row }) => {
                    return this.__(`${ORDER_STATUS[row["status"]]}`, "wepos");
                },
            },
            {
                field: "customer",
                key: "d",
                title: this.__("Customer", "wepos"),
                align: "left",
            },
            {
                field: "gateway",
                key: "e",
                title: this.__("Gateway", "wepos"),
                align: "left",
                renderBodyCell: ({ row }) => {
                    return this.__(`${row["gateway"]}`, "wepos");
                },
            },
            {
                field: "date",
                key: "f",
                title: this.__("Date", "wepos"),
                align: "left",
            },
            {
                field: "",
                key: "actions",
                title: "",
                width: 80,
                renderBodyCell: ({ row }, h) => {
                    return h(ActionsButton, {
                        props: {
                            actionId: row["id"],
                            status: row["status"],
                        },
                        on: {
                            onEditAction: this.editOrder,
                            onRefundAction: this.refundOrder,
                            onReopenAction: this.reopenOrder,
                        },
                    });
                },
            },
        ];
        this.fetchOrders();
    },
};
</script>

<style lang="less" scoped>
.table-pagination {
    margin-top: 20px;
    text-align: right;
}
.empty-data {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
    width: 100%;
    color: #666;
    font-size: 16px;
    border: 1px solid #eee;
    border-top: 0;
}
</style>
