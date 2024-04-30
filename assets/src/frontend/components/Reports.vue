<template>
    <div class="page page__wrapper reports reports__contents">
        <div class="flex justify--sb">
            <div>
                <h1 class="page__title">{{ __("Reports", "wepos") }}</h1>
                <div class="flex filter-container">
                    <date-range-picker
                        ref="picker"
                        :opens="'right'"
                        :locale-data="{
                            firstDay: 1,
                            format: 'dd-mm-yyyy HH:mm:ss',
                        }"
                        :timePicker="false"
                        :showWeekNumbers="true"
                        :showDropdowns="true"
                        v-model="dateRange"
                    >
                        <template v-slot:input="picker">
                            {{ picker.startDate | date }} -
                            {{ picker.endDate | date }}
                        </template>
                    </date-range-picker>
                    <button
                        @click="filterReport"
                        class="wepos-btn wepos-btn-primary"
                    >
                        {{ __("Filter", "wepos") }}
                    </button>
                </div>
            </div>
            <div class="flex align--end">
                <ul class="filter-period">
                    <li>
                        <a
                            :class="{ selected: period === 'week' }"
                            @click="filterByPeriod('week')"
                            >Week</a
                        >
                    </li>
                    <li>
                        <a
                            :class="{ selected: period === 'month' }"
                            @click="filterByPeriod('month')"
                            >Month</a
                        >
                    </li>
                    <li>
                        <a
                            :class="{ selected: period === 'last_month' }"
                            @click="filterByPeriod('last_month')"
                            >Last Month</a
                        >
                    </li>
                    <li>
                        <a
                            :class="{ selected: period === 'year' }"
                            @click="filterByPeriod('year')"
                            >Year</a
                        >
                    </li>
                </ul>
            </div>
        </div>
        <div>
            <div class="row">
                <div class="col-1">
                    <div class="reports__sales">
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                {{ __("Total Order", "wepos") }}
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ formatPrice(reportSales.total_sales) }}
                            </div>
                        </div>
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                {{ __("Net Order", "wepos") }}
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ formatPrice(reportSales.net_sales) }}
                            </div>
                        </div>
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                {{ __("Total Refund", "wepos") }}
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ formatPrice(reportSales.total_refunds) }}
                            </div>
                        </div>
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                {{ __("Average Order", "wepos") }}
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ formatPrice(reportSales.average_sales) }}
                            </div>
                        </div>
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                {{ __("Total POS Discount", "wepos") }}
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ formatPrice(reportSales.total_discount) }}
                            </div>
                        </div>

                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                {{ __("Total Shipping Amount", "wepos") }}
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ formatPrice(reportSales.total_shipping) }}
                            </div>
                        </div>
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                {{ __("Total Number Of Orders", "wepos") }}
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ reportSales.total_orders }}
                            </div>
                        </div>
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                {{ __("Total Product Sale", "wepos") }}
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ reportSales.total_items }}
                            </div>
                        </div>
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                {{ __("Total Tax Amount", "wepos") }}
                            </h4>
                            <div class="reports__sale-item__content">
                                {{ formatPrice(reportSales.total_tax) }}
                            </div>
                        </div>
                        <div class="reports__sale-item">
                            <h4 class="reports__sale-item__title">
                                {{ __("Total Shipping Tax", "wepos") }}
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
                        <div class="col-1 mr-2">
                            <div class="card">
                                <div class="header">
                                    <h3>
                                        {{ __("Total Sale", "wepos") }}
                                    </h3>
                                </div>
                                <div class="body">
                                    <LineChartGenerator
                                        :chart-data="dataTotalSale"
                                        :chart-options="chartOptions"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="card">
                                <div class="header">
                                    <h3>
                                        {{ __("Total Orders", "wepos") }}
                                    </h3>
                                </div>
                                <div class="body">
                                    <LineChartGenerator
                                        :chart-data="dataTotalOrderSale"
                                        :chart-options="chartOptions"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-1">
                            <div class="card">
                                <div class="header">
                                    <h3>
                                        {{ __("Orders", "wepos") }}
                                    </h3>
                                </div>
                                <div class="body">
                                    <ve-table
                                        :columns="orderColumns"
                                        :table-data="orderTableData"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
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
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ORDER_STATUS } from "@/const";
import {
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LineElement,
    LinearScale,
    PointElement,
    Title,
    Tooltip,
} from "chart.js";

import dayjs from "dayjs";
import { Line as LineChartGenerator } from "vue-chartjs/legacy";
import { FILTER_DATE_FORMAT } from "../../const";
let DateRangePicker = wepos_get_lib("DateRangePicker");

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
);

export default {
    name: "Reports",
    components: { DateRangePicker, LineChartGenerator },
    data() {
        return {
            dataTotalSale: {
                labels: [],
                datasets: [
                    {
                        label: "Total Sale",
                        backgroundColor: "#3b80f4",
                        data: [],
                    },
                ],
            },
            dataTotalOrderSale: {
                labels: [],
                datasets: [
                    {
                        label: "Total Orders",
                        backgroundColor: "#3b80f4",
                        data: [],
                    },
                ],
            },
            chartOptions: { responsive: true, maintainAspectRatio: false },
            period: "", //week, month, last_month and year
            dateRange: { startDate: null, endDate: null },
            orderColumns: [
                {
                    field: "date",
                    key: "date",
                    title: "Date",
                    align: "left",
                    width: 200,
                },
                {
                    field: "id",
                    key: "id",
                    title: "ID",
                },
                {
                    field: "total",
                    key: "total",
                    title: "Total Sale",
                    renderBodyCell: ({ row }) => {
                        return this.formatPrice(row["total"] ?? "0");
                    },
                },
                {
                    field: "netSale",
                    key: "netSale",
                    title: "Net Sale",
                    renderBodyCell: ({ row }) => {
                        return this.formatPrice(row["netSale"] ?? "0");
                    },
                },
                {
                    field: "totalProducts",
                    key: "totalProducts",
                    title: "Total Product",
                    align: "left",
                    width: 150,
                },
                {
                    field: "customer",
                    key: "customer",
                    title: "Customer",
                    align: "left",
                },
                {
                    field: "status",
                    key: "status",
                    title: "Status",
                },
            ],
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
                    renderBodyCell: ({ row }) => {
                        return this.formatPrice(row["total"] ?? "0");
                    },
                },
                {
                    field: "quantity",
                    key: "quantity",
                    title: "Total Unit Sold",
                },
            ],
            tableData: [],
            orderTableData: [],
            reportLoading: false,
            reportSales: {},
            topSellers: [],
            top10Orders: [],
        };
    },
    computed: {
        reportTime() {
            if (this.period) {
                return { period: this.period };
            }
            if (this.dateRange.startDate && this.dateRange.endDate) {
                return {
                    date_min: dayjs(this.dateRange.startDate).format(
                        FILTER_DATE_FORMAT
                    ),
                    date_max: dayjs(this.dateRange.endDate).format(
                        FILTER_DATE_FORMAT
                    ),
                };
            }
            return {};
        },
    },
    methods: {
        filterByPeriod(period) {
            this.period = period;
            this.fetchReportSales();
            this.fetchReportTopSellers();
        },
        filterReport() {
            this.period = "";
            this.fetchReportSales();
            this.fetchReportTopSellers();
        },
        normalizeOrders(orders) {
            this.orderTableData = [];
            orders.forEach((order) => {
                const orderData = {
                    id: order.id,
                    order: `Order #${order.id}`,
                    total: this.formatPrice(order.total),
                    status: ORDER_STATUS[order.status] ?? "",
                    date: this.formatDate(order.date_created),
                    totalProducts: order.line_items.reduce(
                        (productCounter, item) =>
                            productCounter + item?.quantity,
                        0
                    ),
                    customer: order.customer_id
                        ? `${order.billing.first_name} ${order.billing.last_name}`
                        : this.__("Guest user", "wepos"),
                };
                this.orderTableData = this.orderTableData.concat(orderData);
            });
        },
        fetchOrders() {
            this.showLoading();
            this.reportLoading = true;
            wepos.api
                .get(
                    wepos.rest.root +
                        wepos.rest.posversion +
                        `/orders?per_page=12&page=1&status=any`
                )
                .done((response) => {
                    this.normalizeOrders(response);
                    this.hideLoading();
                    this.reportLoading = false;
                });
        },
        fetchReportSales() {
            this.showLoading();
            this.reportLoading = true;
            wepos.api
                .get(
                    wepos.rest.root + wepos.rest.posversion + `/reports/sales`,
                    this.reportTime
                )
                .done((response) => {
                    this.reportSales = response[0] ?? {};
                    // Fetch to chart
                    if (this.reportSales.totals) {
                        const labels = [];
                        const sales = [];
                        const totalOrders = [];
                        Object.keys(this.reportSales.totals).forEach(
                            (reportItemKey) => {
                                labels.push(reportItemKey);
                                sales.push(
                                    this.reportSales.totals[reportItemKey].sales
                                );
                                totalOrders.push(
                                    this.reportSales.totals[reportItemKey]
                                        .orders
                                );
                            }
                        );
                        this.dataTotalSale.labels = labels;
                        this.dataTotalSale.datasets[0].data = sales;
                        this.dataTotalOrderSale.labels = labels;
                        this.dataTotalOrderSale.datasets[0].data = totalOrders;
                    }
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
                        `/reports/top_sellers`,
                    this.reportTime
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

        this.fetchOrders();
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
.reports::v-deep .vue-daterange-picker {
    .reportrange-text {
        border-radius: 4px;
        min-width: 235px;
        height: 38px;
        line-height: 28px;
        font-size: 15px;
    }
}
.filter-container {
    gap: 16px;
    align-items: center;
    button {
        min-width: 100px;
        height: 34px;
    }
}
.filter-period {
    list-style-type: none;
    li {
        display: inline-block;
        a {
            cursor:pointer;
            text-decoration: none;
            padding: 4px 15px;
            min-width: 80px;
            display: inline-block;
            text-align: center;
            background-color: white;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            border-radius: 4px;
            &:hover,
            &.selected {
                background-color: var(--primary-color);
                color: white;
            }
        }
    }
}
</style>
