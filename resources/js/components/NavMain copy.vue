<script setup lang="ts">
import {
  SidebarGroup,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem
} from '@/components/ui/sidebar';
import {type NavItem, type SharedData} from '@/types';
import {Link, usePage} from '@inertiajs/vue3';
import {Icon} from '@iconify/vue'
import SidebarMenuSub from "@/components/ui/sidebar/SidebarMenuSub.vue";
import SidebarMenuSubItem from "@/components/ui/sidebar/SidebarMenuSubItem.vue";
import SidebarMenuSubButton from "@/components/ui/sidebar/SidebarMenuSubButton.vue";
import {ref} from "vue";
import {useSidebar} from '@/components/ui/sidebar/utils';

defineProps<{
  items: NavItem[];
}>();

const page = usePage<SharedData>();
const sidebar = useSidebar();


const openSubmenus = ref<{ [key: string]: boolean }>({});

function toggleSubMenu(title: string) {

  if (sidebar.open.value === false) {
    sidebar.open.value = true;
    openSubmenus.value[title] = true;

  } else {
    openSubmenus.value[title] = !openSubmenus.value[title];
  }

}

function isSubMenuOpen(title: string): boolean {
  return !!openSubmenus.value[title];
}

</script>

<template>
  <SidebarGroup class="px-2 py-0">
    <hr class="mb-5"/>
    <SidebarMenu class="font-medium cursor-pointer">
      <SidebarMenuItem v-for="(item) in items" :key="item.title">
        <SidebarMenuButton size="lg"
                           v-if="item.subMenu"
                           as-child
                           :is-active="isSubMenuOpen(item.title)"
                           :tooltip="item.title"
                           @click="toggleSubMenu(item.title)">
          <div class="flex items-center  w-full select-none justify-between">
            <div v-if="!sidebar.open.value" class="">
              <Icon class="tIcon" :icon="'tabler:' + item.tIcon"/>
            </div>
            <div v-if="sidebar.open.value" class="flex items-center gap-2 select-none w-full">
              <Icon v-if="item.tIcon" class="tIcon" :icon="'tabler:' + item.tIcon"/>
              <span class="select-none">{{ item.title }}</span>
            </div>
            <Icon v-if="sidebar.open.value"
                  :icon="isSubMenuOpen(item.title) ? 'tabler:chevron-up' : 'tabler:chevron-down'"/>
          </div>
        </SidebarMenuButton>

        <SidebarMenuButton size="lg" v-else
                           as-child
                           :is-active="item.href === page.url"
                           :tooltip="item.title">
          <Link v-if="item.href" :href="item?.href">
            <component :is="item.icon"/>
            <Icon v-if="item.tIcon" class="tIcon" :icon="'tabler:'+ item.tIcon"/>
            <span>{{ item.title }}</span>
          </Link>
        </SidebarMenuButton>

        <Transition
            enter-active-class="transition-all duration-300 ease-in-out"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-[500px]"
            leave-active-class="transition-all duration-300 ease-in-out"
            leave-from-class="opacity-100 max-h-[500px]"
            leave-to-class="opacity-0 max-h-0"
        >
          <SidebarMenuSub v-if="item.subMenu && isSubMenuOpen(item.title)" class="select-none">
            <SidebarMenuSubItem
                v-for="(sub, idx) in item.subMenu"
                :key="sub.title + idx"
            >
              <SidebarMenuSubButton class="py-2 h-full" :is-active="sub.href === page.url">
                <Link v-if="sub.href" :href="sub?.href" class="inline-flex w-full h-full">
                  <component :is="sub.icon"/>
                  <Icon v-if="sub.tIcon" class="tIconSub my-auto" :icon="'tabler:'+ sub.tIcon"/>
                  <div class="leading-[2]">&nbsp;{{ sub.title }}</div>
                </Link>
              </SidebarMenuSubButton>
            </SidebarMenuSubItem>
          </SidebarMenuSub>
        </Transition>
      </SidebarMenuItem>
    </SidebarMenu>
  </SidebarGroup>
</template>
<style scoped>
.tIcon {
  width: calc(var(--spacing) * 6);
  height: calc(var(--spacing) * 6);
}

.tIconSub {
  width: calc(var(--spacing) * 5);
  height: calc(var(--spacing) * 5);
}
</style>
