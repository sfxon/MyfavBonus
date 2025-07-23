import template from './myfav-bonus-setup-index.html.twig';

const {Component, Mixin} = Shopware;
const { Criteria } = Shopware.Data;

Component.register('myfav-bonus-setup-index', {
    template,

    inject: ['repositoryFactory', 'acl'],

    mixins: [
        Mixin.getByName('listing'),
    ],

    data() {
        return {
            myfavBonis: null,
            isLoading: true,
            sortBy: 'title',
            sortDirection: 'ASC',
            total: 0,
            searchConfigEntity: 'myfav_bonus',
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    computed: {
        myfavBonusRepository() {
            return this.repositoryFactory.create('myfav_bonus');
        },

        myfavBonusColumns() {
            return [{
                property: 'title',
                dataIndex: 'title',
                allowResize: true,
                routerLink: '',
                label: 'myfav-bonus-setup.page-bonus-setup.columnTitle',
                primary: true,
            },
            {
                property: 'subtitle',
                dataIndex: 'subtitle',
                allowResize: true,
                routerLink: '',
                label: 'myfav-bonus-setup.page-bonus-setup.columnSubtitle',
                primary: true,
            },
            {
                property: 'fromCartPrice',
                dataIndex: 'fromCartPrice',
                allowResize: true,
                routerLink: '',
                label: 'myfav-bonus-setup.page-bonus-setup.columnFromCartPrice',
                primary: true,
            }]
        },

        myfavBonusCriteria() {
            const myfavBonusCriteria = new Criteria(this.page, this.limit);
            myfavBonusCriteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection, this.naturalSorting));
            return myfavBonusCriteria;
        },
    },

    methods: {
        async getList() {
            this.isLoading = true;

            console.log('get list');

            const criteria = await this.addQueryScores(this.term, this.myfavBonusCriteria);

            if (!this.entitySearchable) {
                this.isLoading = false;
                this.total = 0;

                return false;
            }

            if (this.freshSearchTerm) {
                criteria.resetSorting();
            }

            return this.myfavBonusRepository.search(criteria)
                .then(searchResult => {
                    console.log('search result: ', searchResult);

                    this.myfavBonis = searchResult;
                    this.total = searchResult.total;
                    this.isLoading = false;
                });
        },

        updateTotal({ total }) {
            this.total = total;
        },
    },
});
