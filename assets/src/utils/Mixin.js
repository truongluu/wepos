import { DATE_FORMAT, DATE_TIME_FORMAT } from "@/const";
import { __, _nx, setLocaleData, sprintf } from "@/utils/i18n";
import dayjs from "dayjs";
export default {
    data() {
        return {
            loadingInstance: null,
        };
    },
    methods: {
        processLoading(edit) {
            if (!this.loadingInstance) {
                this.loadingInstance = this.$veLoading({
                    fullscreen: true,
                    name: "bounce",
                    lock: true,
                });
            }
            if (edit) {
                this.loadingInstance.show();
            } else {
                this.loadingInstance.close();
            }
        },
        showLoading() {
            this.processLoading(true);
        },
        hideLoading() {
            this.processLoading(false);
        },
        setLocaleData(data) {
            return setLocaleData(data);
        },

        __(text, domain) {
            return __(text, domain);
        },

        _nx(single, plural, number, context, domain) {
            return _nx(single, plural, number, context, domain);
        },

        __n(single, plural, number, domain) {
            return _n(single, plural, number, domain);
        },

        sprintf(fmt, ...args) {
            return sprintf(fmt, ...args);
        },

        formatPrice(value) {
            return accounting.formatMoney(
                value,
                wepos.currency_format_symbol,
                wepos.currency_format_num_decimals,
                wepos.currency_format_thousand_sep,
                wepos.currency_format_decimal_sep,
                wepos.currency_format
            );
        },

        formatNumber(value) {
            return accounting.formatNumber(
                value,
                wepos.currency_format_num_decimals,
                wepos.currency_format_thousand_sep,
                wepos.currency_format_decimal_sep
            );
        },
        formatDate(value) {
            return dayjs(value).format(DATE_TIME_FORMAT);
        },

        truncateNumber(value) {
            return parseFloat(
                accounting.toFixed(value, wepos.currency_format_num_decimals)
            );
        },

        unFormat(value) {
            if (value.indexOf(wepos.currency_format_decimal_sep) > -1) {
                return accounting.unformat(
                    value,
                    wepos.currency_format_decimal_sep
                );
            }
            return accounting.unformat(value);
        },

        findMatchingVariations(variations, attributes) {
            var matching = [];
            for (var i = 0; i < variations.length; i++) {
                var variation = variations[i];
                var variationAttributes = {};

                for (var j = 0; j < variation.attributes.length; j++) {
                    variationAttributes[variation.attributes[j].name] =
                        variation.attributes[j].option;
                }

                if (this.isMatch(variationAttributes, attributes)) {
                    matching.push(variation);
                }
            }
            return matching;
        },

        isMatch(variationAttributes, attributes) {
            var match = true;
            for (var attr_name in variationAttributes) {
                if (variationAttributes.hasOwnProperty(attr_name)) {
                    var val1 = variationAttributes[attr_name];
                    var val2 = attributes[attr_name];
                    if (
                        val1 !== undefined &&
                        val2 !== undefined &&
                        val1.length !== 0 &&
                        val2.length !== 0 &&
                        val1 !== val2
                    ) {
                        match = false;
                    }
                }
            }
            return match;
        },

        confirmAlert({ title = "", text = "" }) {
            return this.$swal.fire({
                icon: "warning",
                title: title,
                text: text,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3b80f4",
                cancelButtonColor: "#e34d4d",
                confirmButtonText: this.__("Confirm", "wepos"),
            });
        },
        success({ title, position = "top-right" }) {
            this.toast({ title, type: "success" });
        },
        error({ title, position = "top-right" }) {
            this.toast({ title, type: "error" });
        },
        toast({ title, type = "", position = "top-right" }) {
            const toast = this.$swal.mixin({
                toast: true,
                position,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                animation: true,
            });

            switch (type) {
                case "success":
                    toast.fire({
                        icon: "success",
                        title: title,
                    });
                    break;
                case "error":
                    toast.fire({
                        icon: "error",
                        title: title,
                    });
                    break;
                case "warning":
                    toast.fire({
                        icon: "warning",
                        title: title,
                    });
                    break;
                case "info":
                    toast.fire({
                        icon: "info",
                        title: title,
                    });
                    break;
                default:
                    toast.fire({
                        icon: "success",
                        title: title,
                    });
                    break;
            }
        },

        hasStock(product, productCartQty = 0) {
            if (!product.manage_stock) {
                return "outofstock" !== product.stock_status;
            }

            if (product.backorders_allowed) {
                return true;
            }

            return product.stock_quantity > productCartQty;
        },
        getProductImage(product) {
            return product?.images.length > 0
                ? product?.images[0].woocommerce_thumbnail
                : wepos.placeholder_image;
        },
        getProductImageName(product) {
            return product.images.length > 0
                ? product.images[0].name
                : product.name;
        },
    },
    filters: {
        date(val) {
            return val ? dayjs(val).format(DATE_FORMAT) : "";
        },
    },
    computed: {
        wepos() {
            return wepos;
        },

        eventBus() {
            return wepos_get_lib("EventBus");
        },
    },
};
