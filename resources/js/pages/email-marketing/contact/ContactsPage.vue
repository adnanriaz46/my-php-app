<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, SharedData, } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { useDateFormat } from "@/composables/useFormat";
import { ref, watch } from "vue";
import Button from "@/components/ui/button/Button.vue";
// import { useClipboardCopy } from "@/composables/useCopy";
import { useToast } from "@/composables/useToast";
import Label from "@/components/ui/label/Label.vue";
import Input from "@/components/ui/input/Input.vue";
import { router } from '@inertiajs/vue3';

import { Icon } from '@iconify/vue';
import Pagination from "@/components/ui/pagination/Pagination.vue";
import type { Contact } from '@/types/emailMarketing';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter, DialogClose } from "@/components/ui/dialog";
import SelectBox from '@/components/ui/select-box/SelectBox.vue';
import { useConfirmDialog } from '@/composables/useConfirmDialog';
import { type Errors } from '@inertiajs/core';
import ConfirmDialog from "@/components/ui/confirm-dialog/ConfirmDialog.vue";
import CheckboxLoop from '@/components/ui/checkbox-loop/CheckboxLoop.vue';
import AddEditDialog from '@/components/email-marketing/AddEditDialog.vue';

import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuTrigger,
  DropdownMenuItem,
  DropdownMenuGroup,
  DropdownMenuSeparator
} from "@/components/ui/dropdown-menu";

const { showToast } = useToast()
// const { formatNumber, formatPrice, formatPercent } = useNumber()
const { formatDate } = useDateFormat()
// const { handleCopy, copied } = useClipboardCopy()
const confirmDialog = useConfirmDialog();
const page = usePage<SharedData>();
const props = defineProps<{
  status?: string,
  errors?: any,
  error?: string,
  success?: string,
  counties: string[],
  dealTypes: string[],
  zipCodes: string[],
  tags: string[],
  contacts: {
    data: Contact[],
    links: { url: string | null, label: string, active: boolean }[],
    from: number | null,
    to: number | null,
    current_page: number | null,
    last_page: number | null,
    per_page: number | null,
    total: number | null,
  },
}>();

// const user = computed(() => page.props.auth.user as User);

// const copyLink = (txt: string) => {
//   handleCopy(txt)
//   if (copied) {
//     showToast('Copied', 'Referral Link has been copied!')
//   }
// }

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Contacts',
    href: '/contacts',
  },
];
const selectedContacts = ref<number[]>([]);

const urlParams = new URLSearchParams(window.location.search);
const search = ref<string | undefined>(urlParams.get('search') || undefined);
const selectedCounty = ref<string | null>(urlParams.get('county') || null);
const selectedDealType = ref<string | null>(urlParams.get('deal_type') || null);
const selectedZipCode = ref<string | null>(urlParams.get('zip') || null);
const selectedTag = ref<string | null>(urlParams.get('tag') || null);
const selectedPerPage = ref<number>(Number(urlParams.get('per_page')) || 10);


const perPageOptions = ref<{ key: string, value: string }[]>([
  { key: '10', value: '10' },
  { key: '25', value: '25' },
  { key: '50', value: '50' },
  { key: '100', value: '100' },
  { key: '250', value: '250' },
  { key: '500', value: '500' },
]);

const uploadContactsDialogOpen = ref(false);
const counties = ref<{ key: string, value: string }[]>(props.counties.map((county) => ({ key: county, value: county })));
const dealTypes = ref<{ key: string, value: string }[]>(props.dealTypes.map((dealType) => ({ key: dealType, value: dealType })));
const zipCodes = ref<{ key: string, value: string }[]>(props.zipCodes.map((zipCode) => ({ key: zipCode, value: zipCode })));
const tags = ref<{ key: string, value: string }[]>(props.tags.map((tag) => ({ key: tag, value: tag })));


// Watch for filter changes and send requests
watch([selectedCounty, selectedDealType, selectedZipCode, selectedTag, search, selectedPerPage], ([county, dealType, zipCode, tag, search, perPage]) => {
  const params = new URLSearchParams();

  if (search) params.append('search', search);
  if (county) params.append('county', county);
  if (dealType) params.append('deal_type', dealType);
  if (zipCode) params.append('zip', zipCode);
  if (tag) params.append('tag', tag);
  if (perPage) params.append('per_page', perPage.toString());
  const url = route('email-marketing.contacts') + (params.toString() ? `?${params.toString()}` : '');
  router.visit(url, { preserveScroll: true, preserveState: true });
}, { deep: true });

const addEditDialogOpen = ref(false);
const currentContact = ref<Contact | null>(null);
const isEditing = ref(false);

function editContact(contact: any) {
  currentContact.value = contact;
  isEditing.value = true;
  addEditDialogOpen.value = true;
}

function deleteContact(contact: Contact) {
  // Implement delete logic (confirmation, API call, etc.)
  onHandlingConfirmation(`Delete contact: ${contact.email}`, 'delete', contact.id);
}

const onHandlingConfirmation = async (message: string, type: string, id?: number) => {

  const confirmed = await confirmDialog.openConfirm(message, 'Delete');
  const pageQueryParamsString = page.url.split('?')[1] ?? '';
  const pageQueryParamsArray = Object.fromEntries(new URLSearchParams(pageQueryParamsString))

  if (confirmed) {
    if (type == 'delete') {


      router.delete(route('email-marketing.contacts.delete', { id: id }), {
        preserveUrl: true,
        preserveState: true,
        onSuccess: (data) => {
          if (data.props.success) {
            showToast('Delete', data.props.success as string, 'success');
          } else if (data.props.error) {
            showToast('Delete', data.props.error as string, 'error');
          }
          router.visit(route('email-marketing.contacts', pageQueryParamsArray), {
            preserveScroll: true,
            preserveState: false,
          })
        },
        onError: (errors: Errors) => {
          const messages = Object.values(errors)?.flat()?.join(', ');
          showToast('Delete', messages ?? 'Error deleting contact', 'error')
        }
      });

    } else if (type == 'delete-selected') {
      const formData = new FormData();
      formData.append('ids', selectedContacts.value.join(','));
      router.post(route('email-marketing.contacts.delete-selected'), formData, {
        preserveUrl: true,
        preserveState: true,
        onSuccess: (data) => {
          if (data.props.success) {
            showToast('Delete', data.props.success as string, 'success');
            router.visit(route('email-marketing.contacts', pageQueryParamsArray), {
              preserveScroll: true,
              preserveState: false,
            })
          } else if (data.props.error) {
            showToast('Delete', data.props.error as string, 'error');
          }
          selectedContacts.value = [];
        }
      });
    }
  } else {
    console.log("Cancelled")
  }
}

function downloadTemplate() {
  // Implement download logic (API call, etc.)
  // Download the sample_mail_list.csv file from the public/data directory
  const link = document.createElement('a');
  link.href = page.props.ziggy.url + '/data/sample_mail_list.csv';
  link.download = 'sample_mail_list.csv';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);

  showToast('Download', 'Downloading template...');
}

function onPaginate(url: string) {
  if (url) router.visit(url, { preserveScroll: true, preserveState: true });
}

const uploading = ref(false);
const uploadErrors = ref<string[]>([]);
const uploadSuccess = ref<string>('');
const uploadDoneDialogOpen = ref<boolean>(false);
async function uploadContacts() {
  uploadErrors.value = [];
  uploadSuccess.value = '';
  uploadDoneDialogOpen.value = false;
  uploading.value = true;

  const fileInput = document.getElementById('upload-contacts') as HTMLInputElement;
  const file = fileInput.files?.[0];
  showToast('Upload', 'Uploading contacts...', 'info');

  if (file) {
    const formData = new FormData();
    formData.append('file', file);
    router.post(route('email-marketing.contacts.upload'), formData, {
      onSuccess: (data) => {
        if (data.props.success) {
          showToast('Upload', data.props.success as string, 'success');
          uploadSuccess.value = data.props.success as string;
          uploadDoneDialogOpen.value = true;
          uploadContactsDialogOpen.value = false;
        }
        if (data.props.error) {
          showToast('Upload', data.props.error as string, 'error');
        }
        if (data.props.uploadErrors) {
          uploadErrors.value = data.props.uploadErrors as string[];
        }
      },
      onError: (data) => {
        console.log("error", data);
        showToast('Upload', 'Could not be upload your file.', 'error');
        uploadErrors.value = ['Could not be upload your file.'];
      },
      onFinish: () => {
        uploading.value = false;

        // Any cleanup logic here
      }
    });

  }
}

function deleteSelectedContacts() {
  onHandlingConfirmation(`Delete ${selectedContacts.value.length} contacts`, 'delete-selected');
}

function exportSelectedContacts() {
  const headers = ['email', 'tags', 'phone_number', 'first_name', 'last_name', 'counties_invest', 'deal_type', 'zip_code_invest'];

  const escape = (value: any) => {
    const str = value ?? ''; // null-safe
    return `"${String(str).replace(/"/g, '""')}"`;
  };

  const contacts = props.contacts.data.filter(contact => selectedContacts.value.includes(contact.id));

  const rows = contacts.map(contact => [
    escape(contact.email),
    escape((( contact.tags || []) as string[]).join(',')),
    escape(contact.phone),
    escape(contact.first_name),
    escape(contact.last_name),
    escape((contact.counties || []).join(',')),
    escape((contact.deal_type || []).join(',')),
    escape((contact.zip || []).join(','))
  ]);

  const csvContent = [headers.map(escape), ...rows]
    .map(row => row.join(','))
    .join('\n');

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'contacts_' + new Date().toISOString().split('T')[0] + '.csv';
  a.click();
}

function selectAllContacts() {
  selectedContacts.value = props.contacts.data.map(contact => contact.id);
}

const uncheckLoading = ref(false); // only for uncheck all contacts

function deselectAllContacts() {
  uncheckLoading.value = true;
  selectedContacts.value = [];
  setTimeout(() => {
    uncheckLoading.value = false;
  }, 0);
}


</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <ConfirmDialog :dialog="confirmDialog" />

    <AddEditDialog :counties="counties.map(county => county.value)" :zipCodes="zipCodes.map(zipCode => zipCode.value)"
      :dealTypes="dealTypes.map(dealType => dealType.value)" :tags="tags.map(tag => tag.value)"
      v-model:open="addEditDialogOpen" :contact="currentContact" :is-editing="isEditing" />

    <Dialog v-model:open="uploadContactsDialogOpen">
      <DialogContent>
        <DialogHeader class="space-y-3">
          <DialogTitle>Upload Contacts</DialogTitle>
          <DialogDescription>
            Add your contacts to your list
          </DialogDescription>
        </DialogHeader>
        <div class="grid gap-2" v-show="!uploading">
          <div class="flex flex-col gap-2">
            <Button variant="outline" @click="downloadTemplate()">Download Template</Button>
          </div>
          <Label for="upload-contacts" class="mt-3">Upload Contacts(.csv)</Label>
          <Input type="file" id="upload-contacts" accept=".csv" />
          <div v-if="props.errors?.file"
            class="p-2 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-lg whitespace-pre-wrap text-sm">
            <div v-html="props.errors.file" class="whitespace-pre-wrap"></div>
          </div>

        </div>
        <div class="grid gap-2" v-show="uploading">
          <div class="flex flex-col gap-2 justify-center items-center">
            <Icon icon="tabler:loader" class="size-8 animate-spin" />
            <div class="text-sm text-gray-500 dark:text-gray-400">Uploading contacts...</div>
          </div>
        </div>
        <DialogFooter class="gap-2">
          <DialogClose as-child>
            <Button variant="secondary"> Cancel</Button>
          </DialogClose>

          <Button @click="uploadContacts()" variant="default" :disabled="uploading">
            Upload
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <Dialog v-model:open="uploadDoneDialogOpen">
      <DialogContent>
        <DialogHeader class="space-y-3">
          <DialogTitle>Upload Completed</DialogTitle>
        </DialogHeader>
        <div class="grid gap-2">
          <div class="flex flex-col gap-2">
            <div class="text-sm text-gray-500 dark:text-gray-400">Upload Log:</div>
            <div
              class="text-sm p-2 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-lg whitespace-pre-wrap "
              v-if="uploadSuccess" v-html="uploadSuccess"></div>

            <div class="text-sm text-gray-500 dark:text-gray-400">Error Log:</div>
            <div
              class="text-sm max-h-[200px] overflow-y-auto p-2 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-lg whitespace-pre-wrap"
              v-if="uploadErrors.length > 0">
              <div v-for="error in uploadErrors" :key="error"
                class="text-red-500 dark:text-red-400 whitespace-pre-wrap">
                {{ error }}
              </div>
            </div>
          </div>
        </div>
        <DialogFooter class="gap-2">
          <DialogClose as-child>
            <Button variant="secondary"> Close</Button>
          </DialogClose>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <Head title="Contacts" />
    <div class="flex flex-col gap-2 p-4">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
        <div class="text-xl font-bold">Contact List</div>
        <div class="flex gap-2">
          <Button variant="default" @click="addEditDialogOpen = true; isEditing = false">+ Add Contact</Button>
          <Button variant="outline" @click="uploadContactsDialogOpen = true">Upload Contacts</Button>

        </div>
      </div>

      <div class="flex flex-col md:flex-row gap-2">
        <div class="flex flex-col md:flex-row gap-2">
          <SelectBox v-model="selectedCounty" :options="counties" placeholder="Select County" />
          <SelectBox v-model="selectedDealType" :options="dealTypes" placeholder="Select Deal Type" />
          <SelectBox v-model="selectedZipCode" :options="zipCodes" placeholder="Select Zip Code" />
          <SelectBox v-model="selectedTag" :options="tags" placeholder="Select Tag" />
        </div>
      </div>
      <div class="flex flex-col md:flex-row gap-2 md:justify-between">
        <Label class="my-auto flex justify-between md:justify-start gap-2 order-2 md:order-1">
          <div>Per Page</div>
          <SelectBox size="xs" v-model="selectedPerPage" :options="perPageOptions" placeholder="Select Per Page"
            :class="`m-0 ml-2 w-fit`" />
          <div class="text-sm text-gray-500 dark:text-gray-400 mr-2 my-auto min-w-[150px] ml-2 text-right md:text-left">
            <div v-if="props.contacts.from && props.contacts.to && props.contacts.total" class="w-full">
              {{ props.contacts.from }} - {{ props.contacts.to }} of {{ props.contacts.total }} contacts
            </div>
            <div v-else>
              {{ props.contacts.total }}
            </div>
          </div>
        </Label>
        <Input v-model="search" placeholder="Search by name, email, or phone"
          class="w-full md:w-60 my-1 order-1 md:order-2" />
      </div>

      <div class="overflow-auto rounded-lg border max-h-[calc(100vh-320px)] min-w-full max-w-full">
        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm  ">
          <thead class="bg-gray-100 dark:bg-gray-800 dark:text-white sticky top-0">
            <tr>
              <th class="px-4 py-2 relative">
                <div class="absolute top-0 right-0 bg-primary text-black rounded-md px-2 py-1 text-xs font-normal"
                  v-if="selectedContacts.length > 0">
                  <span class="text-xs font-normal">{{ selectedContacts.length }}</span>
                </div>
                <DropdownMenu>
                  <DropdownMenuTrigger as-child class="w-fit justify-center relative">

                    <Button size="xs" variant="outline" class="w-fit justify-center p-1">
                      <Icon icon="tabler:caret-up-down" class="iconAttr" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent class="w-[160px] rounded-lg" :side="'bottom'" align="start" :side-offset="4">
                    <DropdownMenuGroup>
                      <DropdownMenuItem :as-child="true">
                        <Button @click="deleteSelectedContacts()" class="w-full flex justify-start" variant="ghost"
                          :disabled="selectedContacts.length === 0">
                          <Icon class="size-5" icon="tabler:trash"></Icon>
                          Delete
                        </Button>
                      </DropdownMenuItem>
                      <DropdownMenuItem :as-child="true">
                        <Button @click="exportSelectedContacts()" class="w-full flex justify-start" variant="ghost"
                          :disabled="selectedContacts.length === 0">
                          <Icon class="size-5" icon="tabler:table-export"></Icon>
                          Export
                        </Button>
                      </DropdownMenuItem>
                      <DropdownMenuSeparator />
                      <DropdownMenuItem :as-child="true">
                        <Button @click="selectAllContacts()" class="w-full flex justify-start" variant="ghost">
                          <Icon class="size-5" icon="tabler:select-all"></Icon>
                          Select All
                        </Button>
                      </DropdownMenuItem>
                      <DropdownMenuItem :as-child="true">
                        <Button @click="deselectAllContacts()" class="w-full flex justify-start" variant="ghost"
                          :disabled="selectedContacts.length === 0">
                          <Icon class="size-5" icon="tabler:deselect"></Icon>
                          Deselect All
                        </Button>
                      </DropdownMenuItem>
                    </DropdownMenuGroup>
                  </DropdownMenuContent>
                </DropdownMenu>
              </th>
              <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-white uppercase">Email</th>
              <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-white uppercase">Name</th>
              <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-white uppercase">Phone</th>
              <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-white uppercase">Tags</th>
              <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-white uppercase">Counties</th>
              <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-white uppercase">Deal Type</th>
              <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-white uppercase">Updated At
              </th>
              <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-white uppercase">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="contact in props.contacts.data" :key="contact.id"
              class="hover:bg-gray-100 dark:hover:bg-gray-700">
              <td class="px-2 py-2 w-10">
                <CheckboxLoop v-if="!uncheckLoading" v-model="selectedContacts" :value="contact.id"
                  class="cursor-pointer m-auto" />
              </td>
              <td class="px-2 py-2">{{ contact.email }}</td>
              <td class="px-2 py-2">{{ contact.name }}</td>
              <td class="px-2 py-2">{{ contact.phone }}</td>
              <td class="px-2 py-2">
                <span v-for="tag in (contact.tags)" :key="tag" :class="tag === 'Skip-Traced' ?
                  (contact.has_contact_info ?
                    'bg-green-100 text-green-800 border-green-200 dark:bg-green-900 dark:text-green-200 dark:border-green-700' :
                    'bg-orange-100 text-orange-800 border-orange-200 dark:bg-orange-900 dark:text-orange-200 dark:border-orange-700') :
                  'bg-yellow-200 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'"
                  class="inline-block text-xs px-2 py-1 rounded mr-1 mb-1 border">
                  {{ tag }}
                </span>
              </td>
              <td class="px-2 py-2">
                <span v-for="county in contact.counties || []" :key="county"
                  class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">{{ county }}</span>
              </td>
              <td class="px-2 py-2">
                <span v-for="deal in contact.deal_type || []" :key="deal"
                  class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-1 mb-1">{{ deal }}</span>
              </td>
              <td class="px-2 py-2">{{ formatDate(contact.updated_at) }}</td>
              <td class="px-2 py-2 flex gap-2">
                <Button size="sm" variant="outline" @click="editContact(contact)">
                  <Icon icon="tabler:edit" class="size-4" />
                </Button>
                <Button size="sm" variant="destructive" @click="deleteContact(contact)">
                  <Icon icon="tabler:trash" class="size-4" />
                </Button>
              </td>
            </tr>
            <tr v-if="props.contacts.data.length === 0">
              <td colspan="8" class="text-center py-4 text-gray-400">No contacts found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-end mt-2" v-if="props.contacts.links && props.contacts.links.length > 0">
        <div class="text-sm text-gray-500 dark:text-gray-400 mr-2 my-auto hidden md:block">
          {{ props.contacts.from }} - {{ props.contacts.to }} of {{ props.contacts.total }} contacts found
        </div>
        <Pagination :links="props.contacts.links" @navigate="onPaginate" />

      </div>
    </div>

  </AppLayout>
</template>