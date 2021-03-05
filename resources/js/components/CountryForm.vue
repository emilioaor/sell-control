<template>
    <div class="container">
        <form class="techland-form" @submit.prevent="validateForm()">
            <div class="card">
                <div class="card-header">
                    <div v-if="editData">
                        <i class="fa fa-edit"></i> {{ t('form.edit') }} {{ t('menu.countries') }}
                    </div>
                    <div v-else>
                        <i class="fa fa-plus"></i> {{ t('form.add') }} {{ t('menu.countries') }}
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
                            <label for="name">{{ t('validation.attributes.iso') }}</label>
                            <input
                                    type="text"
                                    id="iso"
                                    name="iso"
                                    class="form-control"
                                    :class="{'is-invalid': errors.has('iso') || exists}"
                                    v-model="form.iso"
                                    v-validate
                                    data-vv-rules="required|regex:^[A-Z][A-Z]$"
                                    maxlength="2"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('iso', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'iso'}) }}</strong>
                            </span>

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('iso', 'regex')">
                                <strong>{{ t('validation.regex', {attribute: 'iso'}) }}</strong>
                            </span>

                            <span class="invalid-feedback" role="alert" v-if="exists">
                                <strong>{{ t('validation.unique', {attribute: 'iso'}) }}</strong>
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
            }
        },

        data() {
            return {
                loading: false,
                exists: false,
                form: {
                    name: null,
                    iso: null,
                }
            }
        },

        methods: {
            validateForm() {
                this.$validator.validateAll().then(res => res && this.checkIfCountryExists());
            },

            checkIfCountryExists() {
                this.loading = true;

                ApiService.post('/admin/country/exists', {iso: this.form.iso})
                    .then(res => {
                        if (!res.data.data || (this.editData && this.editData.id === res.data.data.id)) {
                            this.sendForm();
                        } else {
                            this.exists = true;
                            this.loading = false;
                        }
                    })
                    .catch(err => {
                        this.loading = false;
                    })
            },

            sendForm() {
                const apiService = this.editData ?
                    ApiService.put('/admin/country/' + this.editData.id, this.form) :
                    ApiService.post('/admin/country', this.form)
                ;

                apiService.then(res => {
                    if (res.data.success) {
                        location.href = res.data.redirect;
                    }
                }).catch(err => {
                    this.loading = false;
                })
            }
        },

        watch: {
            "form.iso"(value, old) {
                this.exists = false;
            }
        }
    }
</script>

<style scoped>
    .bg-header {
        background-color: #ddd;
    }
</style>
