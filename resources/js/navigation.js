// resources/js/navigation.js
import {
    HomeIcon,
    PlusIcon,
    BuildingOffice2Icon,
  } from '@heroicons/vue/24/outline'

  export default [
    {
      textKey: 'dashboard',
      icon: HomeIcon,
      routeName: 'dashboard',
      permission: null,
    },
    {
      textKey: 'createUser',
      icon: PlusIcon,
      routeName: 'users.create',
      permission: 'users.create',
    },
    {
      textKey: 'offices',
      icon: BuildingOffice2Icon,
      routeName: 'offices.index',
      permission: 'offices.view',
    },
    // add more items as needed
  ]
