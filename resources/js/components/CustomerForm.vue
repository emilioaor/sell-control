<template>
    <div class="container">
        <form @submit.prevent="validateForm()">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-plus"></i> {{ t('form.add') }} {{ t('menu.customers') }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 col-md-4 form-group">
                            <label for="name">{{ t('validation.attributes.name') }}</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control"
                                :class="{'is-invalid': errors.has('name')}"
                                v-model="form.name"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('name', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'name'}) }}</strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-md-4 form-group">
                            <label for="name">{{ t('validation.attributes.email') }}</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                :class="{'is-invalid': errors.has('email')}"
                                v-model="form.email"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('email', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'name'}) }}</strong>
                            </span>
                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('email', 'email')">
                                <strong>{{ t('validation.required', {attribute: 'email'}) }}</strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-md-4 form-group">
                            <label for="phone">{{ t('validation.attributes.phone') }}</label>
                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                class="form-control"
                                :class="{'is-invalid': errors.has('phone')}"
                                v-model="form.phone"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('phone', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'phone'}) }}</strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-md-4 form-group">
                            <label for="status">{{ t('validation.attributes.status') }}</label>
                            <select
                                name="status"
                                id="status"
                                class="form-control"
                            >
                                <option
                                    v-for="(label, value) in statusAvailable"
                                    :value="value">
                                    {{ label }}
                                </option>
                            </select>
                        </div>

                        <div class="col-sm-6 col-md-4 form-group">
                            <label>{{ t('validation.attributes.country') }}</label>
                            <search-input
                                route="/seller/country"
                                :description-fields="['name']"
                                @selectResult="changeCountry($event)"
                                :input-class="errors.has('country_id') ? 'is-invalid' : ''"
                                :value="country ? country.searchDescription : ''"
                                :read-only="false"
                            ></search-input>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button v-if="!loading" class="btn btn-success">
                        <i class="fa fa-save"></i>
                        {{ t('form.save') }}
                    </button>

                    <i v-if="loading" class="spinner-border spinner-border-sm"></i>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        props: {
            statusAvailable: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                loading: false,
                country: null,
                form: {
                    name: null,
                    email: null,
                    phone: null,
                    status: 'contact',
                    country_id: null
                }
            }
        },

        methods: {
            validateForm() {

            },

            changeCountry(country) {
                this.country = country;
                this.form.country_id = country.id;
            }
        }
    }
</script>
