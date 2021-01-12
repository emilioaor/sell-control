<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-edit"></i>
                {{ t('form.edit') }} {{ t('menu.phoneTypes') }}
            </div>
            <div class="card-body">
                <table class="table table-responsive">
                    <thead>
                        <tr>
                            <th>{{ t('validation.attributes.name') }}</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, i) in form.items">
                            <td>
                                <input
                                    type="text"
                                    :name="'name' + i"
                                    :id="'name' + i"
                                    class="form-control"
                                    :class="{'is-invalid': errors.has('name' + i)}"
                                    v-model="item.name"
                                    v-validate
                                    data-vv-rules="required"
                                >

                                <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('name' + i, 'required')">
                                    <strong>{{ t('validation.required', {attribute: 'name'}) }}</strong>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger" @click="removeItem(i)">
                                    <i class="fa fa-remove"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">
                                <button type="button" class="btn btn-secondary" @click="addItem()">
                                    <i class="fa fa-plus"></i> {{ t('menu.addNew') }}
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <button v-if="!loading" class="btn btn-success" @click="validateForm()">
                    <i class="fa fa-save"></i>
                    {{ t('form.save') }}
                </button>

                <i v-if="loading" class="spinner-border spinner-border-sm"></i>
            </div>
        </div>
    </div>
</template>

<script>
    import ApiService from "../services/ApiService";

    export default {
        props: {
            items: {
                type: Array,
                required: true
            }
        },

        mounted() {
            this.form.items = [...this.items]
        },

        data() {
            return {
                loading: false,
                form: {
                    items: []
                }
            }
        },

        methods: {
            addItem() {
                this.form.items.push({name: null})
            },

            removeItem(index) {
                this.form.items.splice(index, 1);
            },

            validateForm() {
                this.$validator.validateAll().then(res => res && this.sendForm());
            },

            sendForm() {
                this.loading = true;

                ApiService.post('/admin/phone-type', this.form).then(res => {
                    if (res.data.success) {
                        location.reload();
                    }
                }).catch(err => {
                    this.loading = false;
                })
            }
        }
    }
</script>
