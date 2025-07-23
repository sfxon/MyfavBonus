/*
 * @package inventory
 */

import template from './myfav-bonus-setup-detail.html.twig';

const {Component, Mixin} = Shopware;
const { Criteria } = Shopware.Data;
const { mapPropertyErrors } = Shopware.Component.getComponentHelper();

Component.register('myfav-bonus-setup-detail', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Shopware.Mixin.getByName('placeholder'),
        Shopware.Mixin.getByName('notification'),
        Shopware.Mixin.getByName('validation'),
    ],

    shortcuts: {
        'SYSTEMKEY+S': 'onSave',
        ESCAPE: 'onCancel',
    },

    props: {
        myfavBonusId: {
            type: String,
            required: false,
            default: null,
        },
    },


    data() {
        return {
            myfavBonus: null,
            isLoading: false,
            isSaveSuccessful: false,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier),
        };
    },

    computed: {
        identifier() {
            return this.placeholder(this.myfavBonus, 'name');
        },

        myfavBonusIsLoading() {
            return this.isLoading;
        },

        myfavBonusRepository() {
            return this.repositoryFactory.create('myfav_bonus');
        },

        ...mapPropertyErrors('myfavBonus', ['name']),
    },
    

    watch: {
        myfavBonusId() {
            this.createdComponent();
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            Shopware.ExtensionAPI.publishData({
                id: 'myfav-bonus-setup-detail__myfavBonus',
                path: 'myfavBonus',
                scope: this,
            });
            if (this.myfavBonusId) {
                this.loadEntityData();
                return;
            }

            Shopware.State.commit('context/resetLanguageToDefault');
            this.myfavBonus = this.myfavBonusRepository.create();
            this.isLoading = false;
        },

        async loadEntityData() {
            this.isLoading = true;

            const [myfavBonusResponse] = await Promise.allSettled([
                this.myfavBonusRepository.get(this.myfavBonusId),
            ]);

            if (myfavBonusResponse.status === 'fulfilled') {
                this.myfavBonus = myfavBonusResponse.value;
            }

            if (myfavBonusResponse.status === 'rejected') {
                this.createNotificationError({
                    message: this.$tc(
                        'global.notification.notificationLoadingDataErrorMessage',
                    ),
                });
            }

            this.isLoading = false;
        },

        abortOnLanguageChange() {
            return this.myfavBonusResponse.hasChanges(this.myfavBonus);
        },

        saveOnLanguageChange() {
            return this.onSave();
        },

        onChangeLanguage() {
            this.loadEntityData();
        },

        onSave() {
            this.isLoading = true;

            this.myfavBonusRepository.save(this.myfavBonus).then(() => {
                this.isLoading = false;
                this.isSaveSuccessful = true;
                if (this.myfavBonusId === null) {
                    this.$router.push({ name: 'myfav.bonus.setup.index' });
                    return;
                }

                this.loadEntityData();
            }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    message: this.$tc(
                        'global.notification.notificationSaveErrorMessageRequiredFieldsInvalid',
                    ),
                });
                throw exception;
            });
        },

        onCancel() {
            this.$router.push({ name: 'myfav.bonus.setup.index' });
        },
    },
});
