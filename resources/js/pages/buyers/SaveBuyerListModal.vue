<template>
  <div class="modal-overlay">
    <div class="modal-content bg-white dark:bg-neutral-800 border border-gray-200 dark:border-gray-700">
      <div class="modal-header">
        <span class="text-gray-900 dark:text-white">Save Buyer List</span>
        <button class="close-btn text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" @click="$emit('close')">&times;</button>
      </div>
      <div class="modal-body">
        <label class="modal-radio-label text-gray-900 dark:text-white">
          <input type="checkbox" v-model="addToExisting" :value="true" class="modal-radio" />
          <span class="custom-radio"></span>
          Add to existing list
        </label>
        <!-- If Add to existing list is checked, show dropdown, else show input -->
        <select
          v-if="addToExisting"
          v-model="selectedList"
          class="modal-input bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600"
        >
          <option value="" disabled selected>Select a list</option>
          <option v-for="name in savedLists" :key="name" :value="name">
            {{ name }}
          </option>
        </select>
        <input
          v-else
          type="text"
          v-model="listName"
          placeholder="Type name here"
          class="modal-input bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600"
        />
        <button class="modal-save-btn" @click="saveList">Save</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { saveBuyerList, fetchBuyerLists } from '../../lib/buyerListApi';
import { useToast } from '@/composables/useToast'; 

const { showToast } = useToast();
const emit = defineEmits(['close']);
const props = defineProps<{ buyers: any[] }>();

const savedLists = ref<string[]>([]);
const addToExisting = ref(false);
const listName = ref('');
const selectedList = ref('');

// Fetch lists when modal opens or when addToExisting is checked
watch(addToExisting, async (val) => {
  if (val) {
    const lists = await fetchBuyerLists();
    savedLists.value = lists.map((l: any) => l.name); // Adjust based on your API response
  }
});

async function saveList() {
  const name = addToExisting.value ? selectedList.value : listName.value;
  if (!name || !props.buyers.length) {
    showToast('Error', 'Please select at least one buyer', 'error');
    return;
  }
  await saveBuyerList(name, props.buyers); // send full array of buyer objects!
  showToast('List Saved', 'Your list has been successfully saved', 'success');
  emit('close');
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
.modal-content {
  border-radius: 18px;
  padding: 32px 24px 24px 24px;
  min-width: 340px;
  box-shadow: 0 4px 32px rgba(0,0,0,0.18);
  position: relative;
}

/* Dark theme shadow */
.dark .modal-content {
  box-shadow: 0 4px 32px rgba(0,0,0,0.4);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 1.2rem;
  font-weight: bold;
  margin-bottom: 18px;
}
.close-btn {
  background: none;
  border: none;
  font-size: 2rem;
  cursor: pointer;
  line-height: 1;
}
.modal-body {
  display: flex;
  flex-direction: column;
  gap: 18px;
}
.modal-radio-label {
  display: flex;
  align-items: center;
  font-size: 1.1rem;
  font-weight: 500;
  cursor: pointer;
  position: relative;
  margin-bottom: 8px;
}
.modal-radio {
  opacity: 0;
  position: absolute;
  left: 0;
}
.custom-radio {
  width: 22px;
  height: 22px;
  border: 2px solid #ffb84d;
  border-radius: 50%;
  margin-right: 10px;
  display: inline-block;
  position: relative;
  background: #fff;
}

/* Dark theme radio button */
.dark .custom-radio {
  background: #374151; /* gray-700 */
}

.modal-radio:checked + .custom-radio::after {
  content: '';
  display: block;
  width: 12px;
  height: 12px;
  background: #ffb84d;
  border-radius: 50%;
  position: absolute;
  top: 3px;
  left: 3px;
}
.modal-input {
  width: 100%;
  padding: 12px 16px;
  border-radius: 10px;
  border: 1px solid;
  font-size: 1rem;
  margin-bottom: 8px;
}

.modal-input::placeholder {
  color: #6b7280; /* gray-500 */
}

.dark .modal-input::placeholder {
  color: #9ca3af; /* gray-400 */
}

.modal-save-btn {
  background: #fff;
  color: #ffb84d;
  border: 2px solid #ffb84d;
  border-radius: 18px;
  padding: 8px 32px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  margin-top: 8px;
  transition: background 0.2s, color 0.2s;
}

/* Dark theme save button */
.dark .modal-save-btn {
  background: #374151; /* gray-700 */
  color: #ffb84d;
  border-color: #ffb84d;
}

.modal-save-btn:hover {
  background: #ffb84d;
  color: #fff;
}

.dark .modal-save-btn:hover {
  background: #ffb84d;
  color: #1f2937; /* gray-800 for better contrast */
}
</style>