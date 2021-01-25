<template>
    <div class="container">
        <form novalidate @submit.prevent="validateForm()">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-list-alt"></i>
                    {{ t('menu.report') }} {{ t('menu.customers') }}
                </div>
                <div class="card-body">

                    <div class="row">

                        <div class="col-sm-6 col-md-4 form-group">
                            <label for="start"> {{ t('validation.attributes.startDate') }}</label>

                            <date-picker
                                name = "start"
                                id = "start"
                                language="en"
                                input-class = "form-control date-picker"
                                format = "dd/MM/yyyy"
                                v-model="start"
                                @input="form.start = changeDate($event, 0, 0, 0)"

                            ></date-picker>
                        </div>

                        <div class="col-sm-6 col-md-4 form-group">
                            <label for="end"> {{ t('validation.attributes.endDate') }}</label>

                            <date-picker
                                name = "end"
                                id = "end"
                                language="en"
                                input-class = "form-control date-picker"
                                format = "dd/MM/yyyy"
                                v-model="end"
                                @input="form.end = changeDate($event, 23, 59, 59)"

                            ></date-picker>
                        </div>

                        <div class="col-sm-6 col-md-4 form-group">
                            <label> {{ t('validation.attributes.sellers') }}</label>

                            <vue-multiselect
                                :options="sellers"
                                v-model="form.sellers"
                                label="name"
                                track-by="id"
                                :multiple="true"
                                :taggable="true"
                                placeholder=""
                            ></vue-multiselect>
                        </div>
                    </div>


                </div>
                <div class="card-footer">
                    <i v-if="loading" class="spinner-border spinner-border-sm"></i>

                    <button v-else class="btn btn-success">
                        <i class="fa fa-list-alt"></i>
                        {{ t('form.report') }}
                    </button>
                </div>

                <div class="card-body" v-if="data.length">

                    <div class="card form-group" v-for="seller in data">
                        <div class="card-header bg-header">
                            <strong>{{ t('validation.attributes.seller') }}:</strong>
                            {{ seller.name }}
                        </div>
                        <div class="card-body">

                            <div>
                                <span class="bg-warning rounded p-1">
                                    <strong>{{ t('status.contact') }}: {{ seller.count.contact }}</strong>
                                </span>
                                <span class="bg-info rounded p-1">
                                    <strong>{{ t('status.prospect') }}: {{ seller.count.prospect }}</strong>
                                </span>
                                <span class="bg-success rounded p-1">
                                    <strong>{{ t('status.active') }}: {{ seller.count.active }}</strong>
                                </span>
                            </div>

                            <div v-for="(customer, i) in seller.customers">

                                <hr>

                                <div class="row">
                                    <div class="col-sm-6 col-md-auto">
                                        <strong>{{ t('validation.attributes.customer') }}:</strong>
                                        {{ customer.name }}
                                    </div>

                                    <div class="col-sm-6 col-md-auto pl-md-3">
                                        <strong>{{ t('validation.attributes.currentStatus') }}:</strong>
                                        {{ t('status.' + customer.status) }}
                                    </div>
                                </div>

                                <div
                                    v-for="(history, ii) in customer.customer_status_histories"
                                    v-if="(new Date(history.created_at)) >= currentStart && (new Date(history.created_at)) <= currentEnd"
                                >
                                    <div class="row">
                                        <div class="col-md-auto pt-3 pt-md-0 pr-md-2">
                                            {{ (new Date(history.created_at))|date }} -

                                            <template v-if="ii < (customer.customer_status_histories.length - 1)">
                                                <del class="text-danger">
                                                    <strong>{{ t('status.' + customer.customer_status_histories[ii + 1].status) }}</strong>
                                                </del>
                                            </template>
                                            <template v-else>
                                                <del class="text-danger">
                                                    <strong>{{ t('form.new') }}</strong>
                                                </del>
                                            </template>

                                            <i class="fa fa-long-arrow-right"></i>
                                            <span class="text-success">
                                                <strong>{{ t('status.' + history.status) }}</strong>
                                            </span>
                                        </div>

                                        <div class="col-md-auto px-md-0">
                                            <small><i class="fa fa-user d-none d-md-inline"></i></small>
                                            {{ t('form.assignedBy') }}: {{ history.user.name }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import ApiService from "../services/ApiService";

    export default {
        props: {
            sellers: {
                type: Array,
                required: true
            }
        },

        data() {
            return {
                loading: false,
                start: new Date(),
                end: new Date(),
                form: {
                    start: this.changeDate(new Date(), 0, 0, 0),
                    end: this.changeDate(new Date(), 23, 59, 59),
                    sellers: []
                },
                data: [],
                currentStart: null,
                currentEnd: null
            }
        },

        methods: {
            validateForm() {
                this.$validator.validateAll().then(res => res && this.sendForm());
            },

            sendForm() {
                this.data = [];
                this.loading = true;
                this.currentStart = this.form.start;
                this.currentEnd = this.form.end;

                ApiService.post('/admin/customer/report', this.form).then(res => {

                    if (res.data.success) {
                        this.data = this.groupBySeller(res.data.data);
                    }

                    this.loading = false;

                }).catch(err => {
                    this.loading = false;
                })
            },

            changeDate(date, h, i, s) {
                date.setHours(h, i, s);

                return date;
            },

            groupBySeller(customers) {
                const sellers = [];
                let lastSeller = null;
                let customersBySeller = [];

                customers.forEach(customer => {

                    if (customer.seller_id !== lastSeller) {
                        lastSeller = customer.seller_id;
                        customersBySeller = customers.filter(c => c.seller_id === customer.seller_id);

                        sellers.push({
                            ...customer.seller,
                            customers: customersBySeller,
                            count: {
                                contact: customersBySeller.filter(c => c.customer_status_histories[0].status === 'contact').length,
                                prospect: customersBySeller.filter(c => c.customer_status_histories[0].status === 'prospect').length,
                                active: customersBySeller.filter(c => c.customer_status_histories[0].status === 'active').length
                            }
                        })
                    }
                });

                return sellers;
            }
        }
    }
</script>

<style scoped>
    .bg-header {
        background-color: #ddd;
    }
</style>
