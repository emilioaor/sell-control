<template>
    <li class="nav-item dropdown">
        <a
            class="nav-link dropdown-toggle pointer"
            data-toggle="modal"
            data-target="#modalReminder"
            :title="t('form.reminder')"
            @click="getReminders()"
        >
            <i class="fa fa-calendar"></i>
            <span class="bg-success text-white rounded d-inline-block px-1 reminder-count" v-if="remindersToday.length">
                 <small>
                     {{ remindersToday.length }}
                 </small>
            </span>
        </a>

        <!-- Modal -->
        <div class="modal fade" id="modalReminder" tabindex="-1" role="dialog" aria-labelledby="modalReminder" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-12">

                                <div class="text-center" v-if="loading">
                                    <i class="spinner-border"></i>
                                </div>
                                <table v-else class="table table-responsive">
                                    <thead>
                                        <tr>
                                            <th width="1%">{{ t('validation.attributes.date') }}</th>
                                            <th>{{ t('validation.attributes.customer') }}</th>
                                            <th v-if="user.roles.administrator">{{ t('validation.attributes.seller') }}</th>
                                            <th>{{ t('validation.attributes.subject') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="reminder in reminders"
                                            v-if="(new Date(reminder.date)) > (new Date())"
                                            :class="{'reminder-today': isReminderToday(reminder)}"
                                        >
                                            <td>{{ reminder.date|date }}</td>
                                            <td>
                                                <a :href="'/seller/customer/' + reminder.customer.uuid + '/edit'">
                                                    {{ reminder.customer.name }}
                                                </a>
                                            </td>
                                            <td v-if="user.roles.administrator">
                                                <a :href="'/admin/user/' + reminder.customer.seller.uuid + '/edit'">
                                                    {{ reminder.customer.seller.name }}
                                                </a>
                                            </td>
                                            <td>{{ reminder.subject }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ t('form.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </li>
</template>

<script>
    import ApiService from "../services/ApiService";

    export default {
        props: {
            user: {
                type: Object,
                required: true
            }
        },

        mounted() {
            this.getReminders();
        },

        data() {
            return {
                reminders: [],
                loading: false
            }
        },

        methods: {
            getReminders() {
                this.loading = true;

                ApiService.get('/seller/customer/reminder').then(res => {

                    if (res.data.success) {
                        this.reminders = res.data.data;
                    }

                    this.loading = false;

                }).catch(err => {
                    this.loading = false;
                })
            },

            isReminderToday(reminder) {
                return !! this.remindersToday.find(r => r.id === reminder.id);
            }
        },

        computed: {
            remindersToday() {
                const start = (new Date()).setHours(0, 0, 0);
                const end = (new Date()).setHours(23, 59, 59);

                return this.reminders.filter(reminder => {
                    const date = new Date(reminder.date);
                    const current = new Date();

                    return date >= current && date >= start && date <= end;
                });
            }
        }
    }
</script>


<style scoped lang="scss">
    .reminder-today {
        background-color: rgba(56, 193, 114, 0.5);
    }

    .reminder-count {
        animation-name: r-today;
        animation-duration: .5s;
        animation-iteration-count: 3;
        position: absolute;
    }

    @keyframes r-today {
        0% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
        100% {
            transform: translateY(0);
        }
    }
</style>
