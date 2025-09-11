import {reactive} from 'vue'
import {User} from '@/types';

export const upgradeDialog = reactive({
    upgradeDialogOpen: false as boolean,
    user: null as User | null,
})
