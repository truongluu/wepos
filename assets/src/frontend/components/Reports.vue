<template>
    <div class="reports reports__contents">
        <header>
            <div class="title">
                <h1>{{ __("Reports", "wepos") }}</h1>
            </div>
        </header>
        <div>
            <div class="row">
                <div class="col-1">
                    <div class="reports__sales">
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                Total Order
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ formatPrice(reportSales.total_sales) }}
                            </div>
                        </div>
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">Net Order</h4>
                            <div class="reports__sale-item__content">
                                {{ formatPrice(reportSales.net_sales) }}
                            </div>
                        </div>
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                Total Refund
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ formatPrice(reportSales.total_refunds) }}
                            </div>
                        </div>
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                Average Order
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ formatPrice(reportSales.average_sales) }}
                            </div>
                        </div>
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                Total POS Discount
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ formatPrice(reportSales.total_discount) }}
                            </div>
                        </div>

                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                Total Shipping Amount
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ formatPrice(reportSales.total_shipping) }}
                            </div>
                        </div>
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                Total Number Of Orders
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ reportSales.total_orders }}
                            </div>
                        </div>
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                Total Product Sale
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ reportSales.total_items }}
                            </div>
                        </div>
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                Total Tax Amount
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ formatPrice(reportSales.total_tax) }}
                            </div>
                        </div>
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                Total Shipping Tax
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ formatPrice(reportSales.total_shipping) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-2 mr-2">
                    <div class="row mb-2">
                        <div class="col-1">
                            <div class="card">
                                <div class="header">
                                    <h3>
                                        {{ __("Products", "wepos") }}
                                    </h3>
                                </div>
                                <div class="body">
                                    <ve-table
                                        :columns="columns"
                                        :table-data="tableData"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-1 mr-2">
                            <div class="card">
                                <div class="header">
                                    <h3>
                                        {{ __("Total Sale", "wepos") }}
                                    </h3>
                                </div>
                                <div class="body text-disabled"></div>
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="card">
                                <div class="header">
                                    <h3>
                                        {{ __("Net Sale", "wepos") }}
                                    </h3>
                                </div>
                                <div class="body text-disabled"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Reports",
    data() {
        return {
            columns: [
                {
                    field: "",
                    key: "index",
                    title: "#",
                    renderBodyCell: ({ rowIndex }, h) => {
                        return `${++rowIndex}`;
                    },
                },
                {
                    field: "name",
                    key: "name",
                    title: "Title",
                    align: "left",
                },
                {
                    field: "sku",
                    key: "sku",
                    title: "SKU",
                    align: "left",
                },
                {
                    field: "total",
                    key: "total",
                    title: "Total Sale",
                },
                {
                    field: "quantity",
                    key: "quantity",
                    title: "Total Unit Sold",
                },
            ],
            tableData: [],
            reportLoading: false,
            reportSales: {},
            topSellers: [],
        };
    },
    methods: {
        fetchReportSales() {
            this.showLoading();
            this.reportLoading = true;
            wepos.api
                .get(wepos.rest.root + wepos.rest.posversion + `/reports/sales`)
                .done((response) => {
                    this.reportSales = response[0] ?? {};

                    this.hideLoading();
                })
                .always(() => {
                    this.reportLoading = false;
                    this.hideLoading();
                });
        },
        fetchReportTopSellers() {
            this.showLoading();
            this.reportLoading = true;
            wepos.api
                .get(
                    wepos.rest.root +
                        wepos.rest.posversion +
                        `/reports/top_sellers`
                )
                .done((response) => {
                    this.tableData = response;

                    this.hideLoading();
                })
                .always(() => {
                    this.reportLoading = false;
                    this.hideLoading();
                });
        },
    },
    created() {
        this.fetchReportSales();
        this.fetchReportTopSellers();
    },
};
</script>

<style lang="less" scoped>
.reports {
    &__sales {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        grid-gap: 30px;
        margin: 20px 0;
    }
    &__sale-item {
        background-color: #fff;
        border-radius: 4px;
        padding: 15px;
        &__title {
            margin: 0 0 10px;
            font-weight: 700;
            font-size: 16px;
        }
        &__content {
            font-size: 18px;
        }
    }
}
</style>
