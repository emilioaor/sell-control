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
                                v-model="form.status"
                            >
                                <option
                                    v-for="(label, value) in statusAvailable"
                                    v-if="isStatusVisible(value)"
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

                        <div class="col-sm-6 col-md-2 form-group">
                            <label for="storeQTY">{{ t('validation.attributes.storeQTY') }}</label>
                            <input
                                type="number"
                                min="0"
                                id="storeQTY"
                                name="storeQTY"
                                class="form-control"
                                :class="{'is-invalid': errors.has('storeQTY')}"
                                v-model="form.store.qty"
                                @change="changeStoreQTY()"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('storeQTY', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'storeQTY'}) }}</strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-md-2 form-group">
                            <label for="distributorQTY">{{ t('validation.attributes.wholesalerQTY') }}</label>
                            <input
                                type="number"
                                min="0"
                                id="distributorQTY"
                                name="distributorQTY"
                                class="form-control"
                                :class="{'is-invalid': errors.has('distributorQTY')}"
                                v-model="form.wholesaler.qty"
                                @change="changeWholesalerQTY()"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('distributorQTY', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'distributorQTY'}) }}</strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-md-4 form-group" v-if="user.role === 'administrator'">
                            <label for="seller_id">{{ t('validation.attributes.seller') }}</label>
                            <select
                                name="seller_id"
                                id="seller_id"
                                class="form-control"
                                v-model="form.seller_id"
                            >
                                <option
                                    v-for="seller in sellers"
                                    :value="seller.id">
                                    {{ seller.name }}
                                </option>
                            </select>

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('seller_id', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'seller'}) }}</strong>
                            </span>
                        </div>

                    </div>

                    <div class="row mt-3">

                        <!-- Store -->
                        <div class="col-12 form-group card-store" v-if="form.store.qty > 0">
                            <div class="card">
                                <div class="card-header">
                                    {{ t('validation.attributes.store') }} {{ t('form.information') }}
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2 form-group d-flex align-items-center">
                                            <strong>
                                                {{ t('form.general') }}
                                            </strong>
                                        </div>

                                        <div class="col-sm-6 col-md-3 form-group">
                                            <div class="row">
                                                <div class="col-12 form-group">
                                                    <label for="store_sellers">{{ t('validation.attributes.storeSellers') }}</label>
                                                    <input
                                                        type="number"
                                                        min="0"
                                                        id="store_sellers"
                                                        name="store_sellers"
                                                        class="form-control"
                                                        :class="{'is-invalid': errors.has('store_sellers')}"
                                                        v-model="form.store.store_sellers"
                                                    >

                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('store_sellers', 'required')">
                                                        <strong>{{ t('validation.required', {attribute: 'storeSellers'}) }}</strong>
                                                    </span>
                                                </div>

                                                <div class="col-12 form-group">
                                                    <label for="store_dimension">{{ t('validation.attributes.storeDimension') }}</label>
                                                    <input
                                                        type="number"
                                                        min="0"
                                                        id="store_dimension"
                                                        name="store_dimension"
                                                        class="form-control"
                                                        :class="{'is-invalid': errors.has('store_dimension')}"
                                                        v-model="form.store.store_dimension"
                                                    >

                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('store_dimension', 'required')">
                                                        <strong>{{ t('validation.required', {attribute: 'storeDimension'}) }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-6 form-group">
                                            <label for="observations">{{ t('validation.attributes.observations') }}</label>
                                            <textarea
                                                cols="30"
                                                rows="5"
                                                id="observations"
                                                name="observations"
                                                class="form-control"
                                                :class="{'is-invalid': errors.has('observations')}"
                                                v-model="form.store.observations"
                                            ></textarea>

                                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('observations', 'required')">
                                                <strong>{{ t('validation.required', {attribute: 'observations'}) }}</strong>
                                            </span>
                                        </div>
                                    </div>

                                    <hr>

                                    <div
                                        class="row"
                                        v-for="current in parseInt(form.store.qty)"
                                        v-if="form.store.locations[current-1]"
                                    >
                                        <div class="col-md-2 form-group d-flex align-items-center">
                                            <strong>
                                                {{ t('validation.attributes.store') }} #{{ current }}
                                            </strong>
                                        </div>

                                        <div class="col-sm-6 col-md-3 form-group">
                                            <label>{{ t('validation.attributes.country') }}</label>
                                            <search-input
                                                route="/seller/country"
                                                :description-fields="['name']"
                                                @selectResult="changeStoreCountry($event, current)"
                                                :input-class="errors.has('country_id') ? 'is-invalid' : ''"
                                                :value="form.store.locations[current-1].country ? form.store.locations[current-1].country.searchDescription : ''"
                                                :read-only="false"
                                            ></search-input>
                                        </div>

                                        <div class="col-sm-6 col-md-3 form-group">
                                            <label>{{ t('validation.attributes.province') }}</label>
                                            <search-input
                                                :route="'/seller/province/' + (form.store.locations[current-1].country ? form.store.locations[current-1].country.id : '')"
                                                :description-fields="['name']"
                                                @selectResult="changeStoreProvince($event, current)"
                                                :input-class="errors.has('country_id') ? 'is-invalid' : ''"
                                                :value="form.store.locations[current-1].province ? form.store.locations[current-1].province.searchDescription : ''"
                                                :read-only="false"
                                                :disabled="! form.store.locations[current-1].country"
                                            ></search-input>
                                        </div>

                                        <div class="col-sm-6 col-md-3 form-group">
                                            <label>{{ t('validation.attributes.city') }}</label>
                                            <search-input
                                                :route="'/seller/city/' + (form.store.locations[current-1].province ? form.store.locations[current-1].province.id : '')"
                                                :description-fields="['name']"
                                                @selectResult="changeStoreCity($event, current)"
                                                :input-class="errors.has('country_id') ? 'is-invalid' : ''"
                                                :value="form.store.locations[current-1].city ? form.store.locations[current-1].city.searchDescription : ''"
                                                :read-only="false"
                                                :disabled="! form.store.locations[current-1].province"
                                            ></search-input>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Wholesaler -->
                        <div class="col-12 form-group card-store" v-if="form.wholesaler.qty > 0">
                            <div class="card">
                                <div class="card-header">
                                    {{ t('validation.attributes.wholesaler') }} {{ t('form.information') }}
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2 form-group d-flex align-items-center">
                                            <strong>
                                                {{ t('form.general') }}
                                            </strong>
                                        </div>

                                        <div class="col-md-10">
                                            <div class="row">
                                                <div class="col-sm-6 col-md-3 form-group">
                                                    <label for="office_sellers">{{ t('validation.attributes.officeSellers') }}</label>
                                                    <input
                                                        type="number"
                                                        min="0"
                                                        id="office_sellers"
                                                        name="office_sellers"
                                                        class="form-control"
                                                        :class="{'is-invalid': errors.has('office_sellers')}"
                                                        v-model="form.wholesaler.office_sellers"
                                                    >

                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('office_sellers', 'required')">
                                                <strong>{{ t('validation.required', {attribute: 'officeSellers'}) }}</strong>
                                            </span>
                                                </div>

                                                <div class="col-sm-6 col-md-3 form-group">
                                                    <label for="street_sellers">{{ t('validation.attributes.streetSellers') }}</label>
                                                    <input
                                                        type="number"
                                                        min="0"
                                                        id="street_sellers"
                                                        name="street_sellers"
                                                        class="form-control"
                                                        :class="{'is-invalid': errors.has('street_sellers')}"
                                                        v-model="form.wholesaler.street_sellers"
                                                    >

                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('street_sellers', 'required')">
                                                <strong>{{ t('validation.required', {attribute: 'streetSellers'}) }}</strong>
                                            </span>
                                                </div>

                                                <div class="col-sm-6 col-md-3 form-group">
                                                    <label for="customer_qty">{{ t('validation.attributes.customerQTY') }}</label>
                                                    <input
                                                        type="number"
                                                        min="0"
                                                        id="customer_qty"
                                                        name="customer_qty"
                                                        class="form-control"
                                                        :class="{'is-invalid': errors.has('customer_qty')}"
                                                        v-model="form.wholesaler.customers_qty"
                                                    >

                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('customer_qty', 'required')">
                                                <strong>{{ t('validation.required', {attribute: 'customerQTY'}) }}</strong>
                                            </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-md-2 form-group d-flex align-items-center">
                                            <strong>
                                                {{ t('form.salesByType') }}
                                            </strong>
                                        </div>

                                        <div class="col-md-10 form-group">
                                            <div class="row">
                                                <div
                                                    v-for="phoneType in form.wholesaler.phone_types"
                                                    class="col-sm-6 col-md-3 form-group"
                                                >
                                                    <label for="">{{ phoneType.name }}</label>
                                                    <input type="number" class="form-control" v-model="phoneType.qty">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-md-2 form-group d-flex align-items-center">
                                            <strong>
                                                {{ t('form.salesByBrand') }}
                                            </strong>
                                        </div>

                                        <div class="col-md-10 form-group">
                                            <div class="row">
                                                <div
                                                    v-for="phoneBrand in form.wholesaler.phone_brands"
                                                    class="col-sm-6 col-md-3 form-group"
                                                >
                                                    <label for="">{{ phoneBrand.name }}</label>
                                                    <input type="number" class="form-control" v-model="phoneBrand.qty">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-md-2 form-group d-flex align-items-center">
                                            <strong>
                                                {{ t('form.coverage') }}
                                            </strong>
                                        </div>

                                        <div class="col-md-10 form-group">
                                            <div class="row">
                                                <div class="col-sm-6 col-lg-3 form-group">
                                                    <label>{{ t('validation.attributes.country') }}</label>
                                                    <search-input
                                                        route="/seller/country"
                                                        :description-fields="['name']"
                                                        @selectResult="changeWholesalerCountry($event)"
                                                        :input-class="errors.has('country_id') ? 'is-invalid' : ''"
                                                        :value="form.wholesaler.country ? form.wholesaler.country.searchDescription : ''"
                                                        :read-only="false"
                                                    ></search-input>
                                                </div>

                                                <div class="col-sm-6 col-lg-3 form-group" v-if="form.wholesaler.country">
                                                    <label>{{ t('form.allProvinces') }}</label>
                                                    <div>
                                                        <label>
                                                            <input
                                                                type="radio"
                                                                name="all_provinces"
                                                                :value="true"
                                                                v-model="form.wholesaler.allProvinces"
                                                            >
                                                            {{ t('form.yes') }}
                                                        </label>

                                                        &nbsp;
                                                        <label>
                                                            <input
                                                                type="radio"
                                                                name="all_provinces"
                                                                :value="false"
                                                                v-model="form.wholesaler.allProvinces"
                                                            >
                                                            {{ t('form.no') }}
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 form-group" v-if="! form.wholesaler.allProvinces">
                                                    <label>{{ t('validation.attributes.province') }}</label>
                                                    <vue-multiselect
                                                        :options="provinces"
                                                        v-model="form.wholesaler.provinces"
                                                        label="name"
                                                        track-by="id"
                                                        :multiple="true"
                                                        :taggable="true"
                                                        :loading="provinces.length === 0"
                                                        placeholder=""
                                                    ></vue-multiselect>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button v-if="!loading" class="btn btn-success" @click="validateForm()">
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
    import ApiService from "../services/ApiService";

    export default {
        props: {
            statusAvailable: {
                type: Object,
                required: true
            },
            phoneTypes: {
                type: Array,
                required: true
            },
            phoneBrands: {
                type: Array,
                required: true
            },

            editData: {
                type: Object,
                required: false
            },
            sellers: {
                type: Array,
                required: true
            },
            user: {
                type: Object,
                required: true
            }
        },

        mounted() {
            if (this.editData) {
                this.form = {
                    ...this.form,
                    ...this.editData,
                    store: {
                        ...this.form.store
                    },
                    wholesaler: {
                        ...this.form.wholesaler
                    }
                }

                if (this.editData.store) {
                    this.form.store = {
                        ...this.form.store,
                        ...this.editData.store
                    }
                }

                if (this.editData.wholesaler) {
                    this.form.wholesaler = {
                        ...this.form.wholesaler,
                        ...this.editData.wholesaler
                    }
                }

                if (this.editData.country) {
                    this.country = {
                        ...this.editData.country,
                        searchDescription: this.editData.country.name
                    }
                }

                this.editData.store.cities.forEach(city => {
                    this.form.store.locations.push({
                        country: {
                            ...city.province.country,
                            searchDescription: city.province.country.name
                        },
                        province: {
                            ...city.province,
                            searchDescription: city.province.name
                        },
                        city: {
                            ...city,
                            searchDescription: city.name
                        }
                    });
                })

                this.form.wholesaler.phone_types = this.form.wholesaler.phone_types.map(phoneType => {
                    return {
                        ...phoneType,
                        qty: phoneType.pivot.qty
                    }
                })

                this.form.wholesaler.phone_brands = this.form.wholesaler.phone_brands.map(phoneBrand => {
                    return {
                        ...phoneBrand,
                        qty: phoneBrand.pivot.qty
                    }
                })

                if (this.form.wholesaler.provinces.length) {
                    this.form.wholesaler.country = {
                        ...this.form.wholesaler.provinces[0].country,
                        searchDescription: this.form.wholesaler.provinces[0].country.name
                    }

                    this.updateProvinces(this.form.wholesaler.country).then(() => {
                        this.form.wholesaler.allProvinces = this.provinces.length === this.form.wholesaler.provinces.length;
                    });
                }
            }
        },

        data() {
            return {
                loading: false,
                country: null,
                provinces: [],
                form: {
                    name: null,
                    email: null,
                    phone: null,
                    status: 'contact',
                    country_id: null,
                    seller_id: null,
                    store: {
                        store_sellers: 0,
                        store_dimension: null,
                        observations: null,
                        qty: 0,
                        locations: []
                    },
                    wholesaler: {
                        office_sellers: 0,
                        street_sellers: 0,
                        customers_qty: 0,
                        qty: 0,
                        phone_types: [],
                        phone_brands: [],
                        country: null,
                        provinces: [],
                        allProvinces: true,
                    }
                }
            }
        },

        methods: {
            validateForm() {
                this.sendForm();
            },

            sendForm() {
                this.loading = true;

                const apiService = this.editData ?
                    ApiService.put('/seller/customer/' + this.editData.uuid, this.form) :
                    ApiService.post('/seller/customer', this.form);

                apiService.then(res => {

                    if (res.data.success) {
                        location.href = res.data.redirect;
                    }

                }).catch(err => {
                    this.loading = false;
                })
            },

            changeCountry(country) {
                this.country = country;
                this.form.country_id = country.id;
            },

            changeStoreQTY() {
                const currentLocations = [... this.form.store.locations];

                this.form.store.locations = [];
                for (let x = 0; x < parseInt(this.form.store.qty); x++) {
                    this.form.store.locations.push({
                        country: currentLocations[x] ? currentLocations[x].country : null,
                        province: currentLocations[x] ? currentLocations[x].province : null,
                        city: currentLocations[x] ? currentLocations[x].city : null
                    });
                }
            },

            changeWholesalerQTY() {
                if (this.form.wholesaler.phone_types.length === 0) {
                    this.form.wholesaler.phone_types = this.phoneTypes.map(pt => {
                        return {
                            ...pt,
                            qty: 0
                        }
                    });
                }

                if (this.form.wholesaler.phone_brands.length === 0) {
                    this.form.wholesaler.phone_brands = this.phoneBrands.map(pb => {
                        return {
                            ...pb,
                            qty: 0
                        }
                    })
                }
            },

            changeStoreCountry(country, storeNumber) {
                const index = storeNumber - 1;

                this.form.store.locations[index].country = country;
                this.form.store.locations[index].province = null;
                this.form.store.locations[index].city = null;

                this.form.store.locations.forEach((location, i) => {
                    if (i > index) {
                        this.form.store.locations[i].country = country;
                        this.form.store.locations[i].province = null;
                        this.form.store.locations[i].city = null;
                    }
                })
            },

            changeStoreProvince(province, storeNumber) {
                const index = storeNumber - 1;

                this.form.store.locations[index].province = province;
                this.form.store.locations[index].city = null;

                this.form.store.locations.forEach((location, i) => {
                    if (i > index) {
                        this.form.store.locations[i].province = province;
                        this.form.store.locations[i].city = null;
                    }
                })
            },

            changeStoreCity(city, storeNumber) {
                const index = storeNumber - 1;

                this.form.store.locations[index].city = city;

                this.form.store.locations.forEach((location, i) => {
                    if (i > index) {
                        this.form.store.locations[i].city = city;
                    }
                })
            },

            changeWholesalerCountry(country) {
                this.form.wholesaler.country = country
                this.form.wholesaler.allProvinces = true;
                this.form.wholesaler.provinces = [];
                this.provinces = [];
                this.updateProvinces(country);
            },

            updateProvinces(country) {
                return ApiService.get('/seller/province/' + country.id + '?paginate=false').then((res) => {
                    this.provinces = res.data.data;
                })
            },

            isStatusVisible(status) {
                if (! this.editData) {
                    return true;
                }

                if (this.form.status === 'contact' && ['contact', 'prospect', 'active'].indexOf(status) >= 0) {
                    return true;
                }

                if (this.form.status === 'prospect' && ['prospect', 'active'].indexOf(status) >= 0) {
                    return true;
                }

                if (this.form.status === 'active' && ['active'].indexOf(status) >= 0) {
                    return true;
                }

                return false;
            }
        }
    }
</script>

<style scoped lang="scss">
    .card-store {
        .card-header {
            background-color: #ddd;
        }
    }
</style>
