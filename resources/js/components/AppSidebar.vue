<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
// import NavUser from '@/components/NavUser.vue';
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  useSidebar
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';
import { Icon } from '@iconify/vue';
import { UserTypes } from '@/types/userTypes';
import DropdownMenu from './ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuTrigger from './ui/dropdown-menu/DropdownMenuTrigger.vue';
import { ChevronsUpDown } from 'lucide-vue-next';
import DropdownMenuContent from './ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuItem from './ui/dropdown-menu/DropdownMenuItem.vue';
import DropdownMenuSeparator from './ui/dropdown-menu/DropdownMenuSeparator.vue';
import AppearanceTabs from './AppearanceTabs.vue';

const mainNavItems: NavItem[] = [
  {
    title: 'Property Search',
    href: route('property.search'),
    tIcon: 'home-search'
  },
  {
    title: 'Buyers',
    tIcon: 'user-search',
    prefix: 'buyers',
    subMenu: [
      {
        title: 'Search',
        href: route('buyers.search'),
        tIcon: "input-search"

      },
      {
        title: 'Skip Tracing',
        href: '/buyers/skip-tracing',
        tIcon: 'layers-difference'
      },
    ]
  },
  {
    title: 'MLS Offers',
    tIcon: 'shopping-bag-discount',
    prefix: 'mls-offer',
    restrictedUserType: [UserTypes.FREE],
    subMenu: [
      {
        title: 'PDF/Application',
        href: route('mls-offer.pdf-templates'),
        tIcon: "file-type-pdf",
        restrictedUserType: [UserTypes.FREE],
      },
      {
        title: 'Email Templates',
        href: route('mls-offer.email-templates'),
        tIcon: 'template',
        restrictedUserType: [UserTypes.FREE],
      },
      {
        title: 'Mail Logs',
        href: route('mls-offer.mail-logs'),
        tIcon: 'mail-code',
        restrictedUserType: [UserTypes.FREE],
      }
    ]
  },
  {
    title: 'Contacts',
    href: route('email-marketing.contacts'),
    tIcon: 'address-book',
    restrictedUserType: [UserTypes.FREE],
  },
  {
    title: 'Campaign',
    href: route('email-marketing.campaign'),
    tIcon: 'mailbox',
    restrictedUserType: [UserTypes.FREE],
  },
  // {
  //   title: 'My List',
  //   tIcon: 'file-star',
  //   prefix: 'my-list',
  //   subMenu: [
  //     {
  //       title: 'Properties',
  //       href: route('my_list.properties'),
  //       tIcon: "home-star",
  //     },
  //     {
  //       title: 'Buyers',
  //       href: route('my_list.buyers'),
  //       tIcon: 'user-star',
  //     },
  //     {
  //       title: 'Unlisted',
  //       href: route('my_list.unlisted'),
  //       tIcon: 'cloud-star',
  //     },
  //     {
  //       title: 'Suppressed',
  //       href: route('my_list.suppressed'),
  //       tIcon: 'notes-off',
  //     }
  //   ]
  // },
  {
    title: 'Resources',
    tIcon: 'book',
    prefix: 'learn',
    subMenu: [
      {
        title: 'Revamp University',
        href: route('learn.index'),
        tIcon: "school",
      },
      {
        title: 'Instant Comp',
        href: route('guest.instant_comp'),
        tIcon: 'vs',
      },
      {
        title: 'Revamp Podcast',
        href: '/guest',
        tIcon: 'microphone',
      },
      {
        title: 'AI Assessment',
        href: route('guest.assessment_page'),
        tIcon: 'file-text-spark',
      }
    ]
  },
  {
    title: 'My Reports',
    // href: route('my-reports'),
    tIcon: 'report'
  },
  {
    title: 'My Properties',
    href: route('my_properties'),
    tIcon: 'home-up',
    restrictedUserType: [UserTypes.FREE],
  },
  // {
  //   title: 'My Referrals',
  //   href: route('referrals'),
  //   tIcon: 'link-plus',
  //   restrictedUserType: [UserTypes.FREE],
  // },
  {
    title: 'Market Data',
    href: route('market-data'),
    tIcon: 'chart-bar',
  },

];

const adminNavItems: NavItem[] = [
  {
    title: 'Dashboard',
    href: route('admin.index'),
    tIcon: 'layout-dashboard',
    prefix: 'admin/index'
  },
  {
    title: 'Users',
    href: route('admin.users'),
    tIcon: 'users'
  },
  {
    title: 'Skip Trace Analytics',
    href: route('admin.skip-trace-stats'),
    tIcon: 'chart-bar'
  },
  {
    title: 'Requests',
    tIcon: 'message-question',
    prefix: 'admin/requests',
    subMenu: [
      {
        title: 'Showing',
        href: route('admin.requests.showing'),
        tIcon: 'home-link'
      },
      {
        title: 'AskQuestions',
        href: route('admin.requests.ask-questions'),
        tIcon: 'home-question'
      },
      {
        title: 'Instant Offers',
        href: route('admin.requests.instant-offers'),
        tIcon: 'home-dollar'
      },
      {
        title: 'Contact Us',
        href: route('admin.requests.contact-us'),
        tIcon: 'message-circle'
      }
    ]
  },
  {
    title: 'Default Values',
    tIcon: 'columns',
    prefix: 'admin/default-values',
    subMenu: [
      {
        title: 'Upgrade Features',
        href: route('admin.upgrade-features'),
        tIcon: 'device-ipad-up'
      },
      {
        title: 'County States',
        href: route('admin.county-states'),
        tIcon: 'location-pin'
      },
      {
        title: 'Testimonials',
        href: route('admin.testimonials'),
        tIcon: 'message-star'
      }
    ]
  },
  {
    title: 'Referrals (On-Hold)',
    tIcon: 'link-plus',
    prefix: 'admin/referrals',
    subMenu: [
      {
        title: 'Overview',
        href: route('admin.referrals.index'),
        tIcon: 'link-plus',
      },
      {
        title: 'W9s',
        href: route('admin.referrals.w9s'),
        tIcon: 'file-type-pdf',
      },
      {
        title: 'Earnings',
        href: route('admin.referrals.earnings'),
        tIcon: 'user-dollar',
      }
    ]
  },
  {
    title: 'Support Tickets',
    href: route('admin.support-tickets'),
    tIcon: 'help-circle'
  }
];

const isAdmin = computed(() => {
  return route().current('admin.*');
});

// const footerNavItems: NavItem[] = [
//   {
//     title: 'Github Repo',
//     href: 'https://github.com/laravel/vue-starter-kit',
//     tIcon: 'folder',
//   },
//   {
//     title: 'Documentation',
//     href: 'https://laravel.com/docs/starter-kits',
//     tIcon: 'book-open',
//   },
// ];
const { isMobile, state } = useSidebar();
</script>

<template>
  <Sidebar collapsible="icon" variant="sidebar">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link v-if="!isAdmin" :href="route('home')">
            <AppLogo />
            </Link>
            <Link v-else :href="route('admin.index')">
            <div
              class="flex aspect-square size-8 items-center justify-center rounded-md border-2 border-gray-300 dark:border-gray-700">
              <Icon icon="tabler:briefcase" class="size-6 fill-current" />
            </div>
            <div class="ml-1 grid flex-1 text-left text-sm">
              <span class="mb-0.5 truncate font-semibold leading-none">ADMIN</span>
            </div>
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <NavMain :items="isAdmin ? adminNavItems : mainNavItems" />
    </SidebarContent>

    <SidebarFooter>
      <!-- <NavFooter :items="footerNavItems" /> -->
      <SidebarMenu>
        <SidebarMenuItem>
          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <SidebarMenuButton size="lg"
                class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground">
                <Icon icon="tabler:settings" class="tIcon text-black/90 dark:text-white" />
                <span class="font-medium">More</span>
                <ChevronsUpDown class="ml-auto size-4" />
              </SidebarMenuButton>
            </DropdownMenuTrigger>
            <DropdownMenuContent class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg"
              :side="isMobile ? 'bottom' : state === 'collapsed' ? 'left' : 'bottom'" align="end" :side-offset="4">
              <DropdownMenuItem :as-child="true">
                <Link class="block w-full font-medium" :href="route('my_list.suppressed')" prefetch as="button">
                <Icon icon="tabler:notes-off" class="tIcon text-black/90 dark:text-white" />
                Suppress List
                </Link>
              </DropdownMenuItem>
              <DropdownMenuItem :as-child="true">
                <Link class="block w-full font-medium" :href="route('support.tickets.index')" prefetch as="button">
                <Icon icon="tabler:help-circle" class="tIcon text-black/90 dark:text-white" />
                Support Tickets
                </Link>
              </DropdownMenuItem>
              <DropdownMenuItem>
                <DropdownMenuSeparator />
                <AppearanceTabs :hide-label="true" class="w-full justify-center" />
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>
<style scoped>
.tIcon {
  width: calc(var(--spacing) * 6);
  height: calc(var(--spacing) * 6);
}
</style>