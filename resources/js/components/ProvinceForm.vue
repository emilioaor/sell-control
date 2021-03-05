<template>
    <div class="container">
        <form class="techland-form" @submit.prevent="validateForm()">
            <div class="card">
                <div class="card-header">
                    <div v-if="editData">
                        <i class="fa fa-edit"></i> {{ t('form.edit') }} {{ t('menu.provinces') }}
                    </div>
                    <div v-else>
                        <i class="fa fa-plus"></i> {{ t('form.add') }} {{ t('menu.provinces') }}
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 col-lg-4 form-group">
                            <label for="name">{{ t('validation.attributes.name') }}</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control"
                                :class="{'is-invalid': errors.has('name')}"
                                v-model="form.name"
                                v-validate
                                data-vv-rules="required"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('name', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'name'}) }}</strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-lg-4 form-group">
                            <label>{{ t('validation.attributes.country') }}</label>
                            <search-input
                                    route="/seller/country"
                                    :description-fields="['name']"
                                    @selectResult="changeCountry($event)"
                                    :input-class="errors.has('country') ? 'is-invalid' : ''"
                                    :value="form.country ? form.country.searchDescription : ''"
                                    :read-only="false"
                            ></search-input>
                            <input type="hidden" name="country" v-model="form.country_id" v-validate data-vv-rules="required">

                            <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('country', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'country'}) }}</strong>
                            </span>
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
            editData: {
                type: Object,
                required: false
            }
        },

        mounted() {
            if (this.editData) {
                this.form = {...this.editData};
                this.form.country.searchDescription = this.form.country.name;
            }
        },

        data() {
            return {
                loading: false,
                exists: false,
                form: {
                    name: null,
                    country_id: null,
                    country: null
                }
            }
        },

        methods: {
            validateForm() {
                this.$validator.validateAll().then(res => res && this.sendForm());
            },

            sendForm() {
                const apiService = this.editData ?
                    ApiService.put('/admin/province/' + this.editData.id, this.form) :
                    ApiService.post('/admin/province', this.form)
                ;

                this.loading = true;

                apiService.then(res => {
                    if (res.data.success) {
                        location.href = res.data.redirect;
                    }
                }).catch(err => {
                    this.loading = false;
                })
            },

            changeCountry(country) {
                this.form.country = country;
                this.form.country_id = country.id;
            },
        }
    }
</script>

<style scoped>
    .bg-header {
        background-color: #ddd;
    }
</style>
