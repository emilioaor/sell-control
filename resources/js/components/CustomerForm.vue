<template>
    <div class="container">
        <form @submit.prevent="validateForm()" novalidate>
            <div class="card">
                <div class="card-header">
                    <template v-if="editData">
                        <i class="fa fa-edit"></i> {{ t('form.edit') }}
                    </template>
                    <template v-else>
                        <i class="fa fa-plus"></i> {{ t('form.add') }}
                    </template>

                    {{ t('menu.customers') }}
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
                                :class="{'is-invalid': errors.has('name', 'contact')}"
                                v-model="form.name"
                                v-validate
                                data-vv-rules="required"
                                data-vv-scope="contact"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('name', 'required', 'contact')">
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
                                :class="{
                                    'is-invalid': (errors.has('email', 'active') && isActive) || (errors.has('email_phone', 'contact') && ! isActive)
                                }"
                                v-model="form.email"
                                v-validate
                                data-vv-rules="required|email"
                                data-vv-scope="active"
                            >

                            <!-- Validate contact info for contact status -->
                            <input type="hidden" name="email_phone" value="" v-if="!isActive && !form.email && !form.phone" v-validate data-vv-rules="required" data-vv-scope="contact">


                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('email', 'required', 'active') && isActive">
                                <strong>{{ t('validation.required', {attribute: 'email'}) }}</strong>
                            </span>
                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('email', 'email', 'active') && isActive">
                                <strong>{{ t('validation.required', {attribute: 'email'}) }}</strong>
                            </span>
                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('email_phone', 'required', 'contact') && !isActive">
                                <strong>{{ t('validation.required', {attribute: 'emailOrPhone'}) }}</strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-md-4 form-group">
                            <label for="phone">{{ t('validation.attributes.phone') }}</label>
                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                class="form-control"
                                :class="{'is-invalid': (errors.has('phone', 'active') && isActive) || (errors.has('email_phone', 'contact') && ! isActive)}"
                                v-model="form.phone"
                                v-validate
                                data-vv-rules="required"
                                data-vv-scope="active"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('phone', 'required', 'active') && isActive">
                                <strong>{{ t('validation.required', {attribute: 'phone'}) }}</strong>
                            </span>
                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('email_phone', 'required', 'contact') && !isActive">
                                <strong>{{ t('validation.required', {attribute: 'emailOrPhone'}) }}</strong>
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
                                :input-class="errors.has('country_id', 'active') && isActive ? 'is-invalid' : ''"
                                :value="country ? country.searchDescription : ''"
                                :read-only="false"
                            ></search-input>
                            <input type="hidden" name="country_id" v-model="form.country_id" v-validate data-vv-rules="required" data-vv-scope="active">
                            <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('country_id', 'required', 'active') && isActive">
                                <strong>{{ t('validation.required', {attribute: 'country'}) }}</strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-md-2 form-group">
                            <label>{{ t('validation.attributes.isStore') }}</label>
                            <div>
                                <div
                                    class="store-checkbox text-white d-flex justify-content-start"
                                    @click="showStoreInformation()"
                                    :class="{
                                        'justify-content-end': this.form.store.qty > 0
                                    }"
                                >
                                    <div v-if="form.store.qty" class="bg-success ">
                                        {{ t('form.yes') }}
                                    </div>
                                    <div v-else class="bg-danger">
                                        {{ t('form.no') }}
                                    </div>
                                </div>
                            </div>


                            <input type="hidden" name="store_wholesaler" value="" v-if="! isNaN(parseInt(form.store.qty)) && ! isNaN(parseInt(form.wholesaler.qty)) && (parseInt(form.store.qty) + parseInt(form.wholesaler.qty)) <= 0" v-validate data-vv-rules="required" data-vv-scope="active">

                            <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('store_wholesaler', 'required', 'active') && isActive">
                                <strong>{{ t('validation.required', {attribute: 'storeOrWholesaler'}) }}</strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-md-2 form-group">
                            <label>{{ t('validation.attributes.isWholesaler') }}</label>
                            <div>
                                <div>
                                    <div
                                        class="store-checkbox text-white d-flex justify-content-start"
                                        @click="changeWholesalerQTY()"
                                        :class="{
                                        'justify-content-end': this.form.wholesaler.qty > 0
                                    }"
                                    >
                                        <div v-if="form.wholesaler.qty" class="bg-success ">
                                            {{ t('form.yes') }}
                                        </div>
                                        <div v-else class="bg-danger">
                                            {{ t('form.no') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('store_wholesaler', 'required', 'active') && isActive">
                                <strong>{{ t('validation.required', {attribute: 'storeOrWholesaler'}) }}</strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-md-4 form-group" v-if="user.role === 'administrator'">
                            <label for="seller_id">{{ t('validation.attributes.seller') }}</label>
                            <select
                                name="seller_id"
                                id="seller_id"
                                class="form-control"
                                :class="{'is-invalid': errors.has('seller_id', 'contact')}"
                                v-model="form.seller_id"
                                v-validate
                                data-vv-rules="required"
                                data-vv-scope="contact"
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

                        <!-- Comments -->
                        <div class="col-12 form-group card-store" v-if="editData">
                            <div class="card">
                                <div class="card-header d-flex pointer" @click="accordion.comments = !accordion.comments">
                                    <div class="col-8">
                                        <i class="fa fa-comment"></i>
                                        {{ t('validation.attributes.observations') }}
                                    </div>
                                    <div class="text-right col-4">
                                        <i class="fa fa-caret-up" v-if="accordion.comments"></i>
                                        <i class="fa fa-caret-down" v-else></i>
                                    </div>
                                </div>
                                <div class="card-body" v-show="accordion.comments">
                                    <div class="row">
                                        <div class="col-12 form-group">
                                            <label for="newObservation">{{ t('validation.attributes.newObservation') }}</label>
                                            <textarea
                                                name="newObservation"
                                                id="newObservation"
                                                cols="30"
                                                rows="3"
                                                class="form-control"
                                                v-model="form.newObservation"
                                            ></textarea>
                                        </div>
                                        <div class="col-12" v-for="customerObservation in form.customer_observations">
                                            <hr>
                                            <div>
                                                <small><strong>
                                                    {{ customerObservation.created_at|date(true) }} -
                                                    {{ customerObservation.user.name }}
                                                </strong></small>
                                            </div>
                                            <div v-html="customerObservation.observation.replace(/(?:\r\n|\r|\n)/g, '<br />')"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reminder -->
                        <div class="col-12 form-group card-store" v-if="editData">
                            <div class="card">
                                <div class="card-header d-flex pointer" @click="accordion.reminder = !accordion.reminder">
                                    <div class="col-8">
                                        <i class="fa fa-calendar"></i>
                                        {{ t('form.reminder') }}
                                    </div>
                                    <div class="text-right col-4">
                                        <i class="fa fa-caret-up" v-if="accordion.reminder"></i>
                                        <i class="fa fa-caret-down" v-else></i>
                                    </div>
                                </div>
                                <div class="card-body" v-show="accordion.reminder">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-3 form-group">
                                            <label>{{ t('validation.attributes.date') }}</label>
                                            <date-picker
                                                name = "reminder"
                                                id = "reminder"
                                                language="en"
                                                input-class = "form-control date-picker"
                                                format = "MM/dd/yyyy"
                                                v-model="form.reminder.date"
                                                :disabled="{to: toDate}"
                                                @input="changeHour()"
                                            ></date-picker>
                                        </div>

                                        <div class="col-sm-6 col-md-3 form-group">
                                            <label>{{ t('validation.attributes.hour') }}</label>
                                            <input
                                                type="time"
                                                class="form-control"
                                                @change="changeHour()"
                                                v-model="form.reminder.time"
                                                :disabled="! form.reminder.date"
                                            >
                                        </div>

                                        <div class="col-md-5 form-group">
                                            <label for="subject">{{ t('validation.attributes.subject') }}</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="subject"
                                                id="subject"
                                                maxlength="255"
                                                v-model="form.reminder.subject"
                                            >
                                        </div>
                                    </div>
                                    <hr v-if="form.customer_reminders.length">
                                    <div class="row">
                                        <div
                                            class="col-12"
                                            v-for="reminder in form.customer_reminders"
                                            v-if="(new Date(reminder.date)) >= (new Date())"
                                        >
                                            <strong>{{ reminder.date|date(true) }}</strong> -
                                            {{ reminder.subject }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Store -->
                        <div class="col-12 form-group card-store" v-if="showStore">
                            <div class="card">
                                <div class="card-header d-flex pointer" @click="accordion.store = !accordion.store">
                                    <div class="col-8">
                                        <i class="fa fa-store"></i>
                                        {{ t('validation.attributes.store') }} {{ t('form.information') }}
                                    </div>
                                    <div class="text-right col-4">
                                        <i class="fa fa-caret-up" v-if="accordion.store"></i>
                                        <i class="fa fa-caret-down" v-else></i>
                                    </div>
                                </div>
                                <div class="card-body" v-show="accordion.store">
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
                                                        :class="{'is-invalid': isActive && errors.has('store_sellers', 'active')}"
                                                        v-model="form.store.store_sellers"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        data-vv-scope="active"
                                                    >

                                                    <span class="invalid-feedback" role="alert" v-if="isActive && errors.firstByRule('store_sellers', 'required', 'active')">
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
                                                        :class="{'is-invalid': isActive && errors.has('store_dimension', 'active')}"
                                                        v-model="form.store.store_dimension"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        data-vv-scope="active"
                                                    >

                                                    <span class="invalid-feedback" role="alert" v-if="isActive && errors.firstByRule('store_dimension', 'required', 'active')">
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
                                                :class="{'is-invalid': isActive && errors.has('observations', 'active')}"
                                                v-model="form.store.observations"
                                                v-validate
                                                data-vv-rules="required"
                                                data-vv-scope="active"
                                            ></textarea>

                                            <span class="invalid-feedback" role="alert" v-if="isActive && errors.firstByRule('observations', 'required', 'active')">
                                                <strong>{{ t('validation.required', {attribute: 'observations'}) }}</strong>
                                            </span>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-md-2 form-group d-flex align-items-center">
                                            <strong>
                                                {{ t('validation.attributes.storeQTY') }}
                                            </strong>
                                        </div>

                                        <div class="col-sm-6 col-md-3 form-group">
                                            <input
                                                type="number"
                                                min="1"
                                                id="storeQTY"
                                                name="storeQTY"
                                                class="form-control"
                                                :class="{'is-invalid': isActive && errors.has('storeQTY', 'active')}"
                                                v-model="form.store.qty"
                                                @change="changeStoreQTY()"
                                                v-validate
                                                data-vv-rules="required|numeric|min_value:1"
                                                data-vv-scope="contact"
                                            >

                                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('storeQTY', 'required', 'active')">
                                                <strong>{{ t('validation.required', {attribute: 'storeQTY'}) }}</strong>
                                            </span>

                                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('storeQTY', 'numeric', 'active')">
                                                <strong>{{ t('validation.required', {attribute: 'storeQTY'}) }}</strong>
                                            </span>

                                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('storeQTY', 'min_value', 'active')">
                                                <strong>{{ t('validation.required', {attribute: 'storeQTY'}) }}</strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div
                                        class="row"
                                        v-for="current in parseInt(form.store.qty)"
                                        v-if="parseInt(form.store.qty) && form.store.locations[current-1]"
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
                                                :input-class="isActive && errors.has('country' + current, 'active') ? 'is-invalid' : ''"
                                                :value="form.store.locations[current-1].country ? form.store.locations[current-1].country.searchDescription : ''"
                                                :read-only="false"
                                            ></search-input>
                                            <input type="hidden" :name="'country' + current" v-if="! form.store.locations[current-1].country" v-validate data-vv-rules="required" data-vv-scope="active">

                                            <span class="invalid-feedback d-block" role="alert" v-if="isActive && errors.firstByRule('country' + current, 'required', 'active')">
                                                <strong>{{ t('validation.required', {attribute: 'country'}) }}</strong>
                                            </span>
                                        </div>

                                        <div class="col-sm-6 col-md-3 form-group">
                                            <label>{{ t('validation.attributes.province') }}</label>
                                            <search-input
                                                :route="'/seller/province/' + (form.store.locations[current-1].country ? form.store.locations[current-1].country.id : '')"
                                                :description-fields="['name']"
                                                @selectResult="changeStoreProvince($event, current)"
                                                :input-class="isActive && errors.has('province' + current, 'active') ? 'is-invalid' : ''"
                                                :value="form.store.locations[current-1].province ? form.store.locations[current-1].province.searchDescription : ''"
                                                :read-only="false"
                                                :disabled="! form.store.locations[current-1].country"
                                            ></search-input>
                                            <input type="hidden" :name="'province' + current" v-if="! form.store.locations[current-1].province" v-validate data-vv-rules="required" data-vv-scope="active">

                                            <span class="invalid-feedback d-block" role="alert" v-if="isActive && errors.firstByRule('province' + current, 'required', 'active')">
                                                <strong>{{ t('validation.required', {attribute: 'province'}) }}</strong>
                                            </span>
                                        </div>

                                        <div class="col-sm-6 col-md-3 form-group">
                                            <label>{{ t('validation.attributes.city') }}</label>
                                            <search-input
                                                :route="'/seller/city/' + (form.store.locations[current-1].province ? form.store.locations[current-1].province.id : '')"
                                                :description-fields="['name']"
                                                @selectResult="changeStoreCity($event, current)"
                                                :input-class="isActive && errors.has('city' + current, 'active') ? 'is-invalid' : ''"
                                                :value="form.store.locations[current-1].city ? form.store.locations[current-1].city.searchDescription : ''"
                                                :read-only="false"
                                                :disabled="! form.store.locations[current-1].province"
                                            ></search-input>
                                            <input type="hidden" :name="'city' + current" v-if="! form.store.locations[current-1].city" v-validate data-vv-rules="required" data-vv-scope="active">

                                            <span class="invalid-feedback d-block" role="alert" v-if="isActive && errors.firstByRule('city' + current, 'required', 'active')">
                                                <strong>{{ t('validation.required', {attribute: 'city'}) }}</strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Wholesaler -->
                        <div class="col-12 form-group card-store" v-if="form.wholesaler.qty > 0">
                            <div class="card">
                                <div class="card-header d-flex pointer" @click="accordion.wholesaler = !accordion.wholesaler">
                                    <div class="col-8">
                                        <i class="fa fa-store-alt"></i>
                                        {{ t('validation.attributes.wholesaler') }} {{ t('form.information') }}
                                    </div>
                                    <div class="text-right col-4">
                                        <i class="fa fa-caret-up" v-if="accordion.wholesaler"></i>
                                        <i class="fa fa-caret-down" v-else></i>
                                    </div>
                                </div>
                                <div class="card-body" v-show="accordion.wholesaler">
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
                                                        :class="{'is-invalid': errors.has('office_sellers', 'contact')}"
                                                        v-model="form.wholesaler.office_sellers"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        data-vv-scope="contact"
                                                    >

                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('office_sellers', 'required', 'contact')">
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
                                                        :class="{'is-invalid': errors.has('street_sellers', 'contact')}"
                                                        v-model="form.wholesaler.street_sellers"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        data-vv-scope="contact"
                                                    >

                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('street_sellers', 'required', 'contact')">
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
                                                        :class="{'is-invalid': errors.has('customer_qty', 'contact')}"
                                                        v-model="form.wholesaler.customers_qty"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        data-vv-scope="contact"
                                                    >

                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('customer_qty', 'required', 'contact')">
                                                        <strong>{{ t('validation.required', {attribute: 'customerQTY'}) }}</strong>
                                                    </span>
                                                </div>

                                                <div class="col-md-9 form-group">
                                                    <label for="observations">{{ t('validation.attributes.observations') }}</label>
                                                    <textarea
                                                        cols="30"
                                                        rows="5"
                                                        id="wholesaler_observations"
                                                        name="wholesaler_observations"
                                                        class="form-control"
                                                        :class="{'is-invalid': isActive && errors.has('wholesaler_observations', 'active')}"
                                                        v-model="form.wholesaler.observations"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        data-vv-scope="active"
                                                    ></textarea>

                                                    <span class="invalid-feedback" role="alert" v-if="isActive && errors.firstByRule('wholesaler_observations', 'required', 'active')">
                                                        <strong>{{ t('validation.required', {attribute: 'observations'}) }}</strong>
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
                                                    v-for="(phoneType, i) in form.wholesaler.phone_types"
                                                    class="col-sm-6 col-md-3 form-group"
                                                >
                                                    <label :for="'phone-type' + i">{{ phoneType.name }}</label>
                                                    <input
                                                        type="number"
                                                        :name="'phone-type' + i"
                                                        :id="'phone-type' + i"
                                                        class="form-control"
                                                        :class="{'is-invalid': errors.has('phone-type' + i, 'contact')}"
                                                        v-model="phoneType.qty"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        data-vv-scope="contact"
                                                    >

                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('phone-type' + i, 'required', 'contact')">
                                                        <strong>{{ t('validation.required', {attribute: phoneType.name}) }}</strong>
                                                    </span>
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
                                                    v-for="(phoneBrand, i) in form.wholesaler.phone_brands"
                                                    class="col-sm-6 col-md-3 form-group"
                                                >
                                                    <label :for="'phone-brand' + i">{{ phoneBrand.name }}</label>
                                                    <input
                                                        type="number"
                                                        :name="'phone-brand' + i"
                                                        :id="'phone-brand' + i"
                                                        class="form-control"
                                                        :class="{'is-invalid': errors.has('phone-brand' + i, 'contact')}"
                                                        v-model="phoneBrand.qty"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        data-vv-scope="contact"
                                                    >

                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('phone-brand' + i, 'required', 'contact')">
                                                        <strong>{{ t('validation.required', {attribute: phoneBrand.name}) }}</strong>
                                                    </span>
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
                                                        :input-class="isActive && errors.has('countryWholesale', 'active') ? 'is-invalid' : ''"
                                                        :value="form.wholesaler.country ? form.wholesaler.country.searchDescription : ''"
                                                        :read-only="false"
                                                    ></search-input>
                                                    <input type="hidden" :name="'countryWholesale'" v-if="! form.wholesaler.country" v-validate data-vv-rules="required" data-vv-scope="active">

                                                    <span class="invalid-feedback d-block" role="alert" v-if="isActive && errors.firstByRule('countryWholesale', 'required', 'active')">
                                                        <strong>{{ t('validation.required', {attribute: 'country'}) }}</strong>
                                                    </span>
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

                                                <div class="col-lg-6 form-group" v-if="form.wholesaler.country && ! form.wholesaler.allProvinces">
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
                                                    <input type="hidden" name="wholesalerProvinces" v-if="!form.wholesaler.provinces.length" v-validate data-vv-rules="required" data-vv-scope="active">

                                                    <span class="invalid-feedback d-block" role="alert" v-if="isActive && errors.firstByRule('wholesalerProvinces', 'required', 'active')">
                                                        <strong>{{ t('validation.required', {attribute: 'province'}) }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Status history -->
                        <div class="col-12 form-group card-store" v-if="editData">
                            <div class="card">
                                <div class="card-header d-flex pointer" @click="accordion.history = !accordion.history">
                                    <div class="col-10">
                                        <i class="fa fa-history"></i>
                                        {{ t('form.statusHistory') }}
                                    </div>
                                    <div class="text-right col-2">
                                        <i class="fa fa-caret-up" v-if="accordion.history"></i>
                                        <i class="fa fa-caret-down" v-else></i>
                                    </div>
                                </div>
                                <div class="card-body" v-show="accordion.history">

                                    <div class="row">
                                        <div class="col-12 form-group" v-for="(history, i) in form.customer_status_histories">
                                            <hr v-if="i > 0">
                                            <div class="d-flex flex-wrap align-items-center">
                                                <span class="d-inline-block bg-danger text-white p-1 rounded mr-sm-2 col-12 col-sm-2 col-lg-1 text-center opacity">
                                                    <template v-if="i < (form.customer_status_histories.length - 1)">
                                                        <del>{{ t('status.' + form.customer_status_histories[i + 1].status) }}</del>
                                                    </template>
                                                    <template v-else>
                                                        <del>{{ t('form.new') }}</del>
                                                    </template>
                                                </span>
                                                <i class="fa fa-long-arrow-right d-none d-sm-inline-block text-center"></i>
                                                <span class="d-inline-block bg-success text-white p-1 rounded ml-sm-2 col-12 col-sm-2 col-lg-1 text-center">
                                                    {{ t('status.' + history.status) }}
                                                </span>
                                                <div class="col-12 col-sm-7 col-lg-8 mt-2 mt-sm-0">
                                                    {{ t('form.assignedBy') }}:
                                                    <strong>{{ history.user.name }}</strong>

                                                    {{ t('form.at') }}:
                                                    <strong>{{ history.created_at|date }}</strong>
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

                    this.showStore = this.editData.store.qty > 0;
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

                this.form.store.cities.forEach(city => {
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

                if (this.form.store.cities.length < this.form.store.qty) {
                    this.changeStoreQTY();
                }

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

                this.accordion.comments = false;
                this.accordion.store = false;
                this.accordion.wholesaler = false;
                this.accordion.history = false;
                this.accordion.reminder = false;

                this.toDate.setDate(this.toDate.getDate() - 1);
            }
        },

        data() {
            return {
                loading: false,
                country: null,
                provinces: [],
                showStore: false,
                backupStore: null,
                toDate: new Date(),
                form: {
                    name: null,
                    email: null,
                    phone: null,
                    status: 'contact',
                    country_id: null,
                    seller_id: null,
                    customer_observations: [],
                    customer_status_histories: [],
                    customer_reminders: [],
                    newObservation: null,
                    store: {
                        store_sellers: 0,
                        store_dimension: 0,
                        observations: 'No comments',
                        qty: 0,
                        locations: [],
                        cities: []
                    },
                    wholesaler: {
                        office_sellers: 0,
                        street_sellers: 0,
                        customers_qty: 0,
                        observations: 'No comments',
                        qty: 0,
                        phone_types: [],
                        phone_brands: [],
                        country: null,
                        provinces: [],
                        allProvinces: false,
                    },
                    reminder: {
                        date: null,
                        time: null,
                        subject: null
                    }
                },
                accordion: {
                    store: true,
                    wholesaler: true,
                    comments: true,
                    history: true,
                    reminder: true
                }
            }
        },

        methods: {
            validateForm() {
                if (this.form.status === 'contact' || this.form.status === 'prospect') {
                    this.$validator.validateAll('contact').then(res => res && this.sendForm());
                } else if (this.form.status === 'active') {
                    this.$validator.validateScopes().then(res => res && this.sendForm());
                }
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

            showStoreInformation() {
                this.showStore = ! this.showStore;

                if (this.showStore) {
                    if (this.backupStore) {
                        this.form.store = {...this.backupStore};
                    } else {
                        this.form.store.qty = 1;
                    }
                } else {
                    this.backupStore = {...this.form.store};
                    this.form.store.qty = 0;
                }

                this.changeStoreQTY();
            },

            changeStoreQTY() {
                const currentLocations = [... this.form.store.locations];

                this.form.store.locations = [];
                let country;
                for (let x = 0; x < parseInt(this.form.store.qty); x++) {

                    country = currentLocations[x] ? currentLocations[x].country : null;

                    if (! country && this.country) {
                        country = {...this.country};
                    }

                    this.form.store.locations.push({
                        country: country,
                        province: currentLocations[x] ? currentLocations[x].province : null,
                        city: currentLocations[x] ? currentLocations[x].city : null
                    });
                }
            },

            changeWholesalerQTY() {
                this.form.wholesaler.qty = this.form.wholesaler.qty > 0 ? 0 : 1;

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

                if (! this.form.wholesaler.country && this.country) {
                    this.form.wholesaler.country = {...this.country};
                    this.changeWholesalerCountry(this.form.wholesaler.country);
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
                this.form.wholesaler.allProvinces = false;
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

                if (this.editData.status === 'contact' && ['contact', 'prospect', 'active'].indexOf(status) >= 0) {
                    return true;
                }

                if (this.editData.status === 'prospect' && ['prospect', 'active'].indexOf(status) >= 0) {
                    return true;
                }

                if (this.editData.status === 'active' && ['active'].indexOf(status) >= 0) {
                    return true;
                }

                return false;
            },

            changeHour(event) {
                if (this.form.reminder.date && this.form.reminder.time) {
                    const part = this.form.reminder.time.split(':');
                    const date = this.form.reminder.date;

                    date.setHours(parseInt(part[0]), parseInt(part[1]), 0);

                    this.form.reminder.date = date;
                }
            }
        },

        computed: {
            isActive() {
                return this.form.status === 'active';
            }
        },

        watch: {
            'form.store.qty'(value, old) {
                if (value === '') {
                    value = 0;
                }

                this.form.store.qty = parseInt(value);
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
    .store-checkbox {
        border: solid 1px #ccc;
        border-radius: 20px;
        cursor: pointer;
        padding: .2rem;
        background-color: #cddd;

        div {
            padding: .3rem .4rem;
            border-radius: 50%;
        }
    }
    .pointer {
        cursor: pointer;
    }
    .opacity {
        opacity: 0.7;
    }
</style>
