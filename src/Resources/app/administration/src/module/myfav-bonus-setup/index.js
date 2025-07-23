Shopware.Module.register('myfav-bonus-setup', {
    type: 'plugin',
    name: 'MyfavBonusSetup',
    title: 'myfav-bonus-setup.page-bonus-setup.title',
    description: 'myfav-bonus-setup.page-bonus-setup.title',
    color: '#F05A29',
    icon: '',

    routes: {
        index: {
            component: 'myfav-bonus-setup-index',
            path: 'index'
        },
        create: {
            component: 'myfav-bonus-setup-detail',
            path: 'create',
            meta: {
                parentPath: 'myfav.bonus.setup.index',
            },
        }
    },

    settingsItem: [
        {
            group: 'plugins',
            to: 'myfav.bonus.setup.index',
            icon: 'regular-cog',
            name: 'myfav-bonus-setup',
            label: 'myfav-bonus-setup.page-bonus-setup.menuTitle'
        }
    ],
});